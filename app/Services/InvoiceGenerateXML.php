<?php

namespace App\Services;

use App\Models\Config;
use App\Models\FormaPago;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;

class InvoiceGenerateXML
{
    public function generateXML($invoiceDetails, $client, $items)
    {
        $config = $this->getStoredConfig();

        if (!$config) {
            return [
                'success' => false,
                'message' => 'Sin configuración almacenada',
            ];
        }

        $accessKey = $this->generateAccessKey(
            $config->ruc,
            $invoiceDetails['fecha'],
            "01",
            $config->ambiente === 'PRUEBAS' ? '1' : '2',
            $config->codigo_establecimiento . ($config->serie_ruc ?? '001'),
            $invoiceDetails['secuencia_factura'],
        );

        $infoTributaria = $this->prepareInfoTributaria($config, $invoiceDetails, $accessKey);
        $infoFactura = $this->prepareInfoFactura($config, $invoiceDetails, $client);
        $detalles = $this->prepareDetalles($items);
        $fechaVencimiento = date('d/m/Y', strtotime($invoiceDetails['fecha'] . ' +30 days'));
        $infoAdicional = $this->prepareInformacionAdicional($client, $fechaVencimiento);

        $xmlArray = array_merge($infoTributaria, $infoFactura, $detalles, $infoAdicional);

        // Utilizando la version 1.1.0 del XML por compatibilidad con el SRI de 2 a 6 decimales
        $result = ArrayToXml::convert($xmlArray, [
            'rootElementName' => 'factura',
            '_attributes' => [
                'id' => 'comprobante',
                'version' => '1.0.0',
            ],
        ], true, 'UTF-8');

        Storage::put("xml/generated/{$accessKey}.xml", $result);

        return [
            'accessKey' => $accessKey,
            'xml' => $result,
        ];
    }

    private function getStoredConfig()
    {
        return Config::first();
    }

    private function generateNumberCode()
    {
        return str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    private function generateAccessKey($ruc, $fechaEmision, $tipoComprobante, $tipoAmbiente, $serie, $numeroComprobante, $tipoEmision = 1)
    {
        $formatFecha = date_create($fechaEmision)->format('dmY');
        $numberCode = $this->generateNumberCode();
        $serie = str_pad($serie, 6, '0', STR_PAD_LEFT);
        $numeroComprobante = str_pad($numeroComprobante, 9, '0', STR_PAD_LEFT);

        $accessKey = "{$formatFecha}{$tipoComprobante}{$ruc}{$tipoAmbiente}{$serie}{$numeroComprobante}{$numberCode}{$tipoEmision}";
        $verificationDigit = $this->calculateVerificationDigit($accessKey);
        return "{$accessKey}{$verificationDigit}";
    }

    private function calculateVerificationDigit($accessKey)
    {
        $result = 0;
        $multiplier = 7;
        $digits = str_split($accessKey);
        $sum = 0;

        foreach ($digits as $i => $digit) {
            $sum += $digit * $multiplier;
            $multiplier > 2 ? $multiplier-- : ($multiplier = 7);
        }

        $result = 11 - ($sum % 11);

        if ($result === 10) {
            $result = 1;
        } elseif ($result === 11) {
            $result = 0;
        }

        return $result;
    }

    private function formatNumber($number, $decimals = 2)
    {
        return round($number, $decimals);
    }

    private function prepareInfoTributaria($config, $invoiceDetails, $accessKey)
    {
        return [
            'infoTributaria' => [
                'ambiente' => $config->ambiente === 'PRUEBAS' ? '1' : '2',
                'tipoEmision' => '1', // siempre es 1 para emisión normal
                'razonSocial' => $config->razon_social,
                'nombreComercial' => $config->nombre_comercial,
                'ruc' => $config->ruc,
                'claveAcceso' => $accessKey,
                'codDoc' => '01', // HARCODED por ahora porque es una factura
                'estab' => str_pad($config->codigo_establecimiento, 3, '0', STR_PAD_LEFT),
                'ptoEmi' => str_pad($config->serie_ruc ?? '001', 3, '0', STR_PAD_LEFT),
                'secuencial' => str_pad($invoiceDetails['secuencia_factura'], 9, '0', STR_PAD_LEFT),
                'dirMatriz' => $config->direccion_matriz,
            ],
        ];
    }

    private function prepareInfoFactura($config, $invoiceDetails, $client)
    {
        $tipoIdentificacion = $client["tipo_identificacion"];
        $identificacion = $client["numero_identificacion"];
        $codigoTipoIdentificacion = "05";
        if ($tipoIdentificacion === 'ruc') {
            $codigoTipoIdentificacion = "04";
        } else if ($tipoIdentificacion === 'pasaporte') {
            $codigoTipoIdentificacion = "06";
        } else if ($identificacion === '9999999999999') {
            $codigoTipoIdentificacion = "07";
        }

        $formasPago = FormaPago::find($invoiceDetails['forma_pago']);

        return [
            'infoFactura' => [
                'fechaEmision' => date_create($invoiceDetails['fecha'])->format('d/m/Y'),
                'dirEstablecimiento' => $config->direccion_establecimiento,
                // 'contribuyenteEspecial' => $config->contribuyente_especial ?: null, // Comentado para evitar que aparezca vacío en XML y porque es opcional
                'obligadoContabilidad' => $config->obligado_contabilidad,
                'tipoIdentificacionComprador' => $codigoTipoIdentificacion,
                'razonSocialComprador' => "{$client->nombres} {$client->apellidos}",
                'identificacionComprador' => $client->numero_identificacion,
                'direccionComprador' => $client->direccion,
                'totalSinImpuestos' => $this->formatNumber($invoiceDetails['subtotal0'] + $invoiceDetails['subtotal5'] + $invoiceDetails['subtotal15'], 2),
                'totalDescuento' => $this->formatNumber(floatval($invoiceDetails['descuento'])),
                'totalConImpuestos' => [
                    'totalImpuesto' => [
                        // IVA 15%
                        [
                            'codigo' => '2',
                            'codigoPorcentaje' => '4',
                            'baseImponible' => round(floatval($invoiceDetails['subtotal15']), 2),
                            'valor' => round(floatval($invoiceDetails['iva15']), 2),
                        ],
                        // IVA 5%
                        [
                            'codigo' => '2',
                            'codigoPorcentaje' => '5',
                            'baseImponible' => round(floatval($invoiceDetails['subtotal5']), 2),
                            'valor' => round(floatval($invoiceDetails['iva5']), 2),
                        ],
                        // IVA 0%
                        [
                            'codigo' => '2',
                            'codigoPorcentaje' => '0',
                            'baseImponible' => round(floatval($invoiceDetails['subtotal0']), 2),
                            'valor' => '0.00',
                        ],
                        // ICE
                        [
                            'codigo' => '3',
                            'codigoPorcentaje' => '0',
                            'baseImponible' => round(floatval($invoiceDetails['ice'] ?? 0.00), 2),
                            'valor' => round(floatval($invoiceDetails['ice'] ?? 0.00), 2),
                        ],
                    ],
                ],
                'propina' => round(floatval($invoiceDetails['propina'] ?? 0.00), 2),
                'importeTotal' => $invoiceDetails['total_factura'],
                'moneda' => 'DOLAR',
                'pagos' => [
                    'pago' => [
                        'formaPago' => $formasPago->codigo,
                        'total' => round(floatval($invoiceDetails['total_factura']), 2),
                        // 'plazo' => '', // Opcional
                        // 'unidadTiempo' => '', // Opcional
                    ]
                ]
            ]
        ];
    }

    private function prepareDetalles($items)
    {
        $detalles = ['detalle' => []];
        foreach ($items as $item) {
            $detalle = [
                'codigoPrincipal' => $item['codigo'],
                'codigoAuxiliar' => $item['codigo'],
                'descripcion' => $item['nombre'],
                'cantidad' => $item['cantidad'],
                'precioUnitario' => $this->formatNumber(floatval($item['precio_unitario']), 2),
                'descuento' => $this->formatNumber(floatval($item['descuento'] ?? 0.00), 2),
                'precioTotalSinImpuesto' => $this->formatNumber(floatval($item['total']), 2),
                'impuestos' => [
                    'impuesto' => [
                        'codigo' => '2',
                        'codigoPorcentaje' => '4',
                        'tarifa' => '15',
                        'baseImponible' => $this->formatNumber(floatval($item['total']), 2),
                        'valor' => $this->formatNumber(floatval($item['total']) * 0.15, 2),
                    ]
                ]
            ];

            // Agregar impuestos si existen
            if (!empty($item['impuestos']) && is_array($item['impuestos'])) {
                foreach ($item['impuestos'] as $impuesto) {
                    $detalle['impuestos']['impuesto'][] = [
                        'codigo' => $impuesto['codigo'],
                        'codigoPorcentaje' => $impuesto['codigo_porcentaje'],
                        'tarifa' => $this->formatNumber(floatval($impuesto['tarifa']), 2),
                        'baseImponible' => $this->formatNumber(floatval($impuesto['base_imponible']), 2),
                        'valor' => $this->formatNumber(floatval($impuesto['valor']), 2),
                    ];
                }
            }

            $detalles['detalle'][] = $detalle;
        }

        return ['detalles' => $detalles];
    }

    private function prepareInformacionAdicional($client, $fechaVencimiento)
    {
        return [
            'infoAdicional' => [
                'campoAdicional' => [
                    [
                        '_attributes' => ['nombre' => 'Nombre'],
                        '_value' => "{$client->nombres} {$client->apellidos}",
                    ],
                    [
                        '_attributes' => ['nombre' => 'Email'],
                        '_value' => $client->email,
                    ],
                    [
                        '_attributes' => ['nombre' => 'Dirección'],
                        '_value' => $client->direccion ?? 'N/A',
                    ],
                    [
                        '_attributes' => ['nombre' => 'Teléfono'],
                        '_value' => $client->telefono ?? 'N/A',
                    ],
                    [
                        '_attributes' => ['nombre' => 'Vencimiento'],
                        '_value' => $fechaVencimiento,
                    ],
                ],

            ]
        ];
    }
}
