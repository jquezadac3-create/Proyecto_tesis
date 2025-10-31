<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use Exception;

/**
 * Implementación XAdES-BES específica para SRI Ecuador
 * Basada en las especificaciones exactas del SRI
 */
class XadesBesSriSigner
{
    private $certificate;

    private $privateKey;

    private $certificateData;

    public function __construct(string $p12Path, string $password)
    {
        if (!file_exists($p12Path)) {
            throw new Exception("Archivo P12 no encontrado: {$p12Path}");
        }

        $p12Content = file_get_contents($p12Path);
        $certificates = [];

        if (!openssl_pkcs12_read($p12Content, $certificates, $password)) {
            throw new Exception("Error al leer certificado P12: " . openssl_error_string());
        }

        $this->certificate = $certificates['cert'];
        $this->privateKey = $certificates['pkey'];

        // Extraer datos del certificado
        $this->extractCertificateData();
    }

    private function extractCertificateData(): void
    {
        $certInfo = openssl_x509_parse($this->certificate);

        if ($certInfo === false) {
            throw new Exception("Error al parsear el certificado");
        }

        // Verificar que issuer y subject existan y sean arrays
        if (!isset($certInfo['issuer']) || !is_array($certInfo['issuer'])) {
            throw new Exception("Información del emisor del certificado no válida");
        }

        if (!isset($certInfo['subject']) || !is_array($certInfo['subject'])) {
            throw new Exception("Información del sujeto del certificado no válida");
        }

        $this->certificateData = [
            'issuer' => $this->formatDN($certInfo['issuer']),
            'subject' => $this->formatDN($certInfo['subject']),
            'serial' => (string)$certInfo['serialNumber'],
            'fingerprint' => base64_encode(openssl_x509_fingerprint($this->certificate, 'sha1', true)),
            'cert_base64' => $this->getCertificateBase64()
        ];

        // Obtener clave pública
        $publicKey = openssl_pkey_get_public($this->certificate);
        if ($publicKey === false) {
            throw new Exception("Error al obtener clave pública");
        }

        $keyDetails = openssl_pkey_get_details($publicKey);
        if ($keyDetails === false) {
            throw new Exception("Error al obtener detalles de la clave");
        }

        $this->certificateData['modulus'] = base64_encode($keyDetails['rsa']['n']);
        $this->certificateData['exponent'] = base64_encode($keyDetails['rsa']['e']);
    }

    private function getCertificateBase64(): string
    {
        // Extraer el certificado en formato PEM
        $certResource = openssl_x509_read($this->certificate);
        if ($certResource === false) {
            throw new Exception("Error al leer el certificado");
        }

        if (!openssl_x509_export($certResource, $certPem)) {
            throw new Exception("Error al exportar el certificado");
        }

        // Extraer solo la parte base64 (sin headers PEM)
        $certPem = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'], '', $certPem);
        $certPem = preg_replace('/\s+/', '', $certPem);

        return $certPem;
    }

    private function formatDN(array $dn): string
    {
        $parts = [];
        $order = ['CN', 'L', 'OU', 'O', 'C'];

        foreach ($order as $key) {
            if (isset($dn[$key])) {
                $value = is_array($dn[$key]) ? $dn[$key][0] : $dn[$key];
                $parts[] = "{$key}={$value}";
            }
        }

        return implode(',', $parts);
    }

    public function signXml(string $xmlContent): array
    {
        // Crear documento original para digest
        $originalDoc = new DOMDocument('1.0', 'UTF-8');
        $originalDoc->preserveWhiteSpace = false;
        $originalDoc->formatOutput = false;
        $originalDoc->loadXML($xmlContent);
        $originalDoc->documentElement->setIdAttribute('id', true);

        // Documento de trabajo
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        $doc->loadXML($xmlContent);

        $rootElement = $doc->documentElement;
        $rootElement->setIdAttribute('id', true);
        $rootId = $rootElement->getAttribute('id');

        // Generar IDs únicos
        $signatureId = 'Signature' . $this->generateId();
        $signedPropsId = "{$signatureId}-SignedProperties";
        $keyInfoId = 'Certificate' . $this->generateId();
        $objectId = "{$signatureId}-Object";
        $refId = 'Reference-ID-' . $this->generateId();

        // Crear estructura de firma
        $signature = $this->createSignature($doc, $signatureId, $rootId, $signedPropsId, $keyInfoId, $refId);
        $rootElement->appendChild($signature);

        // Obtener elementos para cálculo de digest
        $signedInfo = $signature->getElementsByTagName('SignedInfo')->item(0);
        $keyInfo = $signature->getElementsByTagName('KeyInfo')->item(0);
        $signedProps = $doc->getElementById($signedPropsId);

        // Calcular y establecer digest values
        $this->setDigestValues($doc, $originalDoc, $signedProps, $keyInfo);

        // Firmar SignedInfo
        $this->signSignedInfo($doc, $signedInfo);

        return ['xml' => $doc->saveXML(), 'signature' => $signatureId, 'signedPropsId' => $signedPropsId, 'keyInfoId' => $keyInfoId];
    }

    /**
     * Get 8 random digits, only numbers
     * Limited 990 to 99 999
     * @return string
     */
    private function generateId()
    {
        return (string) random_int(990, 99999);
    }

    private function createSignature(DOMDocument $doc, string $signatureId, string $rootId, string $signedPropsId, string $keyInfoId, string $refId): DOMElement
    {
        // ds:Signature
        $signature = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Signature');
        $signature->setAttribute('Id', $signatureId);
        $signature->setIdAttribute('Id', true);

        // ds:SignedInfo
        $signedInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignedInfo');
        $signature->appendChild($signedInfo);

        // CanonicalizationMethod
        $canonMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:CanonicalizationMethod');
        $canonMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canonMethod);

        // SignatureMethod
        $sigMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureMethod');
        $sigMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $signedInfo->appendChild($sigMethod);

        // Referencias en orden específico del SRI
        $this->addSignedPropsReference($doc, $signedInfo, $signedPropsId);
        $this->addKeyInfoReference($doc, $signedInfo, $keyInfoId);
        $this->addDocumentReference($doc, $signedInfo, $rootId, $refId);

        // SignatureValue
        $signatureValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureValue');
        $signatureValue->setAttribute('Id', 'SignatureValue' . $this->generateId());
        $signatureValue->setIdAttribute('Id', true);
        $signature->appendChild($signatureValue);

        // KeyInfo
        $keyInfo = $this->createKeyInfo($doc, $keyInfoId);
        $signature->appendChild($keyInfo);

        // Object con QualifyingProperties
        $object = $this->createQualifyingProperties($doc, $signatureId, $signedPropsId, $refId);
        $signature->appendChild($object);

        return $signature;
    }

    private function addDocumentReference(DOMDocument $doc, DOMElement $signedInfo, string $rootId, string $refId): void
    {
        $reference = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Reference');

        $transforms = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transforms');
        $transform = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transform');
        $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform);
        $reference->appendChild($transforms);

        $digestMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $reference->appendChild($digestMethod);

        $reference->setAttribute('Id', $refId);
        $reference->setIdAttribute('Id', true);
        $reference->setAttribute('URI', "#{$rootId}");
        $signedInfo->appendChild($reference);
    }

    private function addSignedPropsReference(DOMDocument $doc, DOMElement $signedInfo, string $signedPropsId): void
    {
        $reference = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Reference');
        $reference->setAttribute('Type', 'http://uri.etsi.org/01903#SignedProperties');
        $reference->setAttribute('URI', "#{$signedPropsId}");
        $signedInfo->appendChild($reference);

        $digestMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $reference->appendChild($digestMethod);
    }

    private function addKeyInfoReference(DOMDocument $doc, DOMElement $signedInfo, string $keyInfoId): void
    {
        $reference = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Reference');
        $reference->setAttribute('URI', "#{$keyInfoId}");
        $signedInfo->appendChild($reference);

        $digestMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $reference->appendChild($digestMethod);
    }

    private function createKeyInfo(DOMDocument $doc, string $keyInfoId): DOMElement
    {
        $keyInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
        $keyInfo->setAttribute('Id', $keyInfoId);
        $keyInfo->setIdAttribute('Id', true);

        // X509Data
        $x509Data = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
        $x509Cert = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate');
        $x509Cert->nodeValue = chunk_split($this->certificateData['cert_base64'], 76, "\n");
        $x509Data->appendChild($x509Cert);
        $keyInfo->appendChild($x509Data);

        // KeyValue
        $keyValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyValue');
        $rsaKeyValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:RSAKeyValue');

        $modulus = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Modulus');
        $modulus->nodeValue = chunk_split($this->certificateData['modulus'], 76, "\n");
        $rsaKeyValue->appendChild($modulus);

        $exponent = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Exponent');
        $exponent->nodeValue = $this->certificateData['exponent'];
        $rsaKeyValue->appendChild($exponent);

        $keyValue->appendChild($rsaKeyValue);
        $keyInfo->appendChild($keyValue);

        return $keyInfo;
    }

    private function createQualifyingProperties(DOMDocument $doc, string $signatureId, string $signedPropsId, string $refId): DOMElement
    {
        $object = $doc->createElement('ds:Object');

        $qualifyingProps = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:QualifyingProperties');
        $qualifyingProps->setAttribute('Target', "#{$signatureId}");

        $signedProps = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:SignedProperties');
        $signedProps->setAttribute('Id', $signedPropsId);
        $signedProps->setIdAttribute('Id', true);

        // SignedSignatureProperties
        $signedSigProps = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:SignedSignatureProperties');

        // SigningTime
        $now = new DateTime('now', new DateTimeZone('America/Guayaquil'));
        $signingTime = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:SigningTime');
        $signingTime->nodeValue = $now->format('Y-m-d\TH:i:sP');
        $signedSigProps->appendChild($signingTime);

        // SigningCertificate
        $signingCert = $this->createSigningCertificate($doc);
        $signedSigProps->appendChild($signingCert);

        $signedProps->appendChild($signedSigProps);

        // SignedDataObjectProperties
        $signedDataProps = $this->createSignedDataObjectProperties($doc, $refId);
        $signedProps->appendChild($signedDataProps);

        $qualifyingProps->appendChild($signedProps);
        $object->appendChild($qualifyingProps);

        return $object;
    }

    /**
     * Crea el elemento `etsi:SigningCertificate` con los detalles del certificado de firma.
     * Este elemento contiene el `etsi:Cert`, el cual incluye:
     * 1. El `etsi:CertDigest`, que es un digest del certificado en formato SHA-1.
     * 2. El `etsi:IssuerSerial`, que contiene el nombre del emisor y el número de serie del certificado.
     *
     * @param DOMDocument $doc El documento XML en el que se creará el elemento `etsi:SigningCertificate`.
     * 
     * @return DOMElement El nodo `etsi:SigningCertificate` generado con la información del certificado.
     */
    private function createSigningCertificate(DOMDocument $doc): DOMElement
    {
        $signingCert = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:SigningCertificate');
        $cert = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:Cert');

        // CertDigest
        $certDigest = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:CertDigest');
        $digestMethod = $doc->createElement('ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $certDigest->appendChild($digestMethod);

        $digestValue = $doc->createElement('ds:DigestValue');
        $digestValue->nodeValue = $this->certificateData['fingerprint'];
        $certDigest->appendChild($digestValue);
        $cert->appendChild($certDigest);

        // IssuerSerial
        $issuerSerial = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:IssuerSerial');
        $issuerName = $doc->createElement('ds:X509IssuerName');
        $issuerName->nodeValue = $this->certificateData['issuer'];
        $issuerSerial->appendChild($issuerName);

        $serialNumber = $doc->createElement('ds:X509SerialNumber');
        $serialNumber->nodeValue = $this->certificateData['serial'];
        $issuerSerial->appendChild($serialNumber);
        $cert->appendChild($issuerSerial);

        $signingCert->appendChild($cert);
        return $signingCert;
    }

    /**
     * Crea el elemento `etsi:SignedDataObjectProperties` que describe el objeto de datos firmado.
     * Este elemento contiene:
     * 1. El `etsi:DataObjectFormat`, que referencia el objeto de datos firmado con un `ObjectReference` (ID de referencia).
     * 2. La descripción del contenido firmado (`etsi:Description`).
     * 3. El tipo MIME del objeto de datos firmado (`etsi:MimeType`).
     *
     * @param DOMDocument $doc El documento XML en el que se creará el elemento `etsi:SignedDataObjectProperties`.
     * @param string $refId El ID de referencia del objeto de datos firmado.
     * 
     * @return DOMElement El nodo `etsi:SignedDataObjectProperties` generado con la información del objeto de datos.
     */
    private function createSignedDataObjectProperties(DOMDocument $doc, string $refId): DOMElement
    {
        $signedDataProps = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:SignedDataObjectProperties');
        $dataFormat = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:DataObjectFormat');
        $dataFormat->setAttribute('ObjectReference', "#{$refId}");

        $description = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:Description');
        $description->nodeValue = 'contenido comprobante';
        $dataFormat->appendChild($description);

        $mimeType = $doc->createElementNS('http://uri.etsi.org/01903/v1.3.2#', 'etsi:MimeType');
        $mimeType->nodeValue = 'text/xml';
        $dataFormat->appendChild($mimeType);

        $signedDataProps->appendChild($dataFormat);
        return $signedDataProps;
    }

    /**
     * Establece los valores de digest para los elementos firmados dentro del documento XML.
     * Esta función calcula los valores de digest (SHA-1) de tres partes específicas del documento XML:
     * 1. El documento raíz original (sin la firma).
     * 2. Las propiedades firmadas (ds:SignedProperties).
     * 3. La información de clave (ds:KeyInfo).
     * Luego inserta estos valores de digest en los nodos ds:Reference correspondientes.
     *
     * @param DOMDocument $doc El documento XML que contiene todos los nodos firmados, incluidos los elementos ds:Reference.
     * @param DOMDocument $originalDoc El documento XML original (sin el nodo ds:Signature), usado para calcular el digest del documento raíz.
     * @param DOMElement $signedProps El nodo ds:SignedProperties que contiene las propiedades firmadas.
     * @param DOMElement $keyInfo El nodo ds:KeyInfo que contiene la información de clave pública.
     * 
     * @throws Exception Si no se encuentran al menos tres nodos ds:Reference dentro del documento $doc.
     */
    private function setDigestValues(DOMDocument $doc, DOMDocument $originalDoc, DOMElement $signedProps, DOMElement $keyInfo): void
    {
        // Obtener los nodos ds:Reference dentro del documento firmado
        $references = $doc->getElementsByTagNameNS('http://www.w3.org/2000/09/xmldsig#', 'Reference');

        // Verificar que haya al menos tres nodos ds:Reference (ds:SignedProperties - ds:KeyInfo - <documento raíz>)
        if ($references->length < 3) {
            throw new Exception('Se esperaban al menos 3 nodos ds:Reference en el documento.');
        }

        // Obtener y calcular el digest del documento raíz original sin canonicalizar
        $xmlRootString = $originalDoc->documentElement->C14N(false, false);
        $docDigest = base64_encode(hash('sha1', $xmlRootString, true));
        $this->upsertDigestValue($references->item(2), $docDigest);

        // Asegurar que los namespaces estén correctamente definidos en el nodo ds:SignedProperties
        $this->ensureNamespaces($signedProps);
        $signedPropsRaw = $signedProps->C14N(false, false);
        $signedPropsDigest = base64_encode(hash('sha1', $signedPropsRaw, true));
        $this->upsertDigestValue($references->item(0), $signedPropsDigest);

        // Asegurar que los namespaces estén correctamente definidos en el nodo ds:KeyInfo
        $this->ensureNamespaces($keyInfo);
        $keyInfoRaw = $keyInfo->C14N(false, false);
        $keyInfoDigest = base64_encode(hash('sha1', $keyInfoRaw, true));
        $this->upsertDigestValue($references->item(1), $keyInfoDigest);
    }


    /**
     * Firma el elemento ds:SignedInfo utilizando la llave privada
     * @param DOMDocument $doc es el documento XML que contiene todos los nodos ya agregados
     * @param DOMElement $signedInfo es el elemento ds:SignedInfo a firmar
     * @return void
     */
    private function signSignedInfo(DOMDocument $doc, DOMElement $signedInfo): void
    {
        $this->ensureNamespaces($signedInfo);

        $signedInfoRaw = $signedInfo->C14N(false, false);

        // Firma (SHA1 como en tu flujo)
        $signature = '';
        if (!openssl_sign($signedInfoRaw, $signature, $this->privateKey, OPENSSL_ALGO_SHA1)) {
            throw new Exception('Error al firmar: ' . openssl_error_string());
        }

        // Localizar SignatureValue (namespace-aware)
        $signatureValue = $doc
            ->getElementsByTagNameNS('http://www.w3.org/2000/09/xmldsig#', 'SignatureValue')
            ->item(0);
        if (!$signatureValue) {
            throw new Exception('SignatureValue element not found');
        }

        $b64 = base64_encode($signature);
        $wrapped = chunk_split($b64, 76, "\n");

        while ($signatureValue->firstChild) {
            $signatureValue->removeChild($signatureValue->firstChild);
        }
        $signatureValue->appendChild($doc->createTextNode($wrapped));
    }

    /**
     * Asegura que el elemento tenga los namespaces requeridos en el mismo nodo.
     */
    private function ensureNamespaces(DOMElement $el): void
    {
        // Declaraciones xmlns:... (namespace de xmlns = http://www.w3.org/2000/xmlns/)
        $el->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $el->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:etsi', 'http://uri.etsi.org/01903/v1.3.2#');
    }

    /**
     * Crea o actualiza el ds:DigestValue dentro de un ds:Reference.
     */
    private function upsertDigestValue(DOMElement $reference, string $digestB64): void
    {
        $doc = $reference->ownerDocument;
        $dsNS = 'http://www.w3.org/2000/09/xmldsig#';

        $existing = null;
        foreach ($reference->childNodes as $child) {
            if ($child instanceof DOMElement && $child->namespaceURI === $dsNS && $child->localName === 'DigestValue') {
                $existing = $child;
                break;
            }
        }

        if ($existing) {
            while ($existing->firstChild) {
                $existing->removeChild($existing->firstChild);
            }
            $existing->appendChild($doc->createTextNode($digestB64));
        } else {
            $reference->appendChild($doc->createElementNS($dsNS, 'ds:DigestValue', $digestB64));
        }
    }
}