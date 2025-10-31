<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $config = Config::first();

        if (!$config) {
            return view('components.configuracion.form', ['config' => null]);
        }

        $imgRoute = $config->logo_path ? Storage::temporaryUrl(
            $config->logo_path,
            now()->addMinutes(5)
        ) : null;

        $config->firma = Crypt::decryptString($config->firma_contrasenia);
        $config->logo = $imgRoute;

        return view('components.configuracion.form', ['config' => $config]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razon_social' => 'required|string',
            'nombre_comercial' => 'required|string',
            'ruc' => 'required|string|max:13',
            'codigo_establecimiento' => 'required|string',
            'serie_ruc' => 'required|string',
            'direccion_matriz' => 'required|string',
            'direccion_establecimiento' => 'required|string',
            'tipo_contribuyente' => 'required|string',
            'obligado_contabilidad' => 'required|in:SI,NO',
            'ambiente' => 'required|in:PRODUCCION,PRUEBAS',
            'estado_electronica' => 'boolean',
            'contrasenia_firma' => 'required|string',
            'firma_electronica' => 'required|file',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ], 400);
        }

        $savedConfig = Config::all()->count();

        if ($savedConfig !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una configuración guardada. Por favor, edite la configuración existente.',
            ], 400);
        }

        $firma = $request->file('firma_electronica');

        if (!$firma->isValid()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Archivo inválido',
                    'errors' => ['El archivo no se ha subido correctamente.']
                ]
            ], 422);
        }

        if (!in_array($firma->getClientOriginalExtension(), ['p12', 'pfx'])) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => ['El archivo debe tener extensión .p12 o .pfx.']
                ]
            ], 422);
        }

        $password = $request->input('contrasenia_firma');

        $nombreArchivo = $firma->getClientOriginalName();

        $ruta = $firma->storeAs('config/firma', $nombreArchivo);

        $signatureRealPath = Storage::path($ruta);

        $content = file_get_contents($signatureRealPath);

        if (!$content || strlen($content) < 100) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Archivo corrupto',
                    'errors' => ['No se pudo leer el contenido del archivo.']
                ]
            ], 422);
        }

        if (!openssl_pkcs12_read($content, $certs, $password)) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => ['El archivo de firma no es válido o la contraseña es incorrecta.']
                ]
            ], 422);
        }

        $logo = $request->file('logo')->store('config/logo');

        // Remember decrypt the password
        $encryptedPassword = Crypt::encryptString($password);

        $request->merge([
            'firma_contrasenia' => $encryptedPassword,
            'firma_path' => $ruta,
            'logo_path' => $logo,
        ]);

        Config::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'razon_social' => 'sometimes|required|string',
            'nombre_comercial' => 'sometimes|required|string',
            'ruc' => 'sometimes|required|string|max:13',
            'codigo_establecimiento' => 'sometimes|required|string',
            'serie_ruc' => 'sometimes|required|string',
            'direccion_matriz' => 'sometimes|required|string',
            'direccion_establecimiento' => 'sometimes|required|string',
            'tipo_contribuyente' => 'sometimes|required|string',
            'obligado_contabilidad' => 'sometimes|required|in:SI,NO',
            'ambiente' => 'sometimes|required|in:PRODUCCION,PRUEBAS',
            'estado_electronica' => 'sometimes|boolean',
            'contrasenia_firma' => 'sometimes|required|string',
            'firma_electronica' => 'sometimes|required|file',
            'logo' => 'sometimes|nullable|file|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $config = Config::find($id);

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error',
                    'message' => 'No se encontró la configuración.'
                ]
            ], 404);
        }

        $data = $request->all();

        if ($request->hasFile('firma_electronica')) {
            $firma = $request->file('firma_electronica');

            if (!$firma->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => [
                        'title' => 'Archivo inválido',
                        'errors' => ['El archivo no se ha subido correctamente.']
                    ]
                ], 422);
            }

            if (!in_array($firma->getClientOriginalExtension(), ['p12', 'pfx'])) {
                return response()->json([
                    'success' => false,
                    'message' => [
                        'title' => 'Error de validación',
                        'errors' => ['El archivo debe tener extensión .p12 o .pfx.']
                    ]
                ], 422);
            }

            $password = $request->input('contrasenia_firma');
            $nombreArchivo = $firma->getClientOriginalName();
            $ruta = $firma->storeAs('config/firma', $nombreArchivo);
            $signatureRealPath = Storage::path($ruta);
            $content = file_get_contents($signatureRealPath);

            if (!$content || strlen($content) < 100) {
                return response()->json([
                    'success' => false,
                    'message' => [
                        'title' => 'Archivo corrupto',
                        'errors' => ['No se pudo leer el contenido del archivo.']
                    ]
                ], 422);
            }

            if (!openssl_pkcs12_read($content, $certs, $password)) {
                return response()->json([
                    'success' => false,
                    'message' => [
                        'asd' => openssl_error_string(),
                        'title' => 'Error de validación',
                        'errors' => ['El archivo de firma no es válido o la contraseña es incorrecta.']
                    ]
                ], 422);
            }

            $data['firma_path'] = $ruta;
            $data['firma_contrasenia'] = Crypt::encryptString($password);
        }

        if ($request->hasFile('logo')) {
            $config->logo_path ? Storage::delete($config->logo_path) : null;
            $logo = $request->file('logo')->store('config/logo');
            $data['logo_path'] = $logo;
        }

        $updated = $config->update($data);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Configuración actualizada correctamente' : 'No se pudo actualizar la configuración.'
        ]);
    }
}
