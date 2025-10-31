<?php

namespace App\Http\Controllers\Routes;

use App\Http\Controllers\Controller;
use App\Models\FacturaCabecera;
use App\Models\Qr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ScannedQrController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            // new Middleware('permission:manage qr codes', ['index']),
        ];
    }

    public function index(Request $request)
    {
        $qrCode = $request->query('qr_code');

        $savedQr = Qr::find($qrCode);

        if (!$savedQr) {
            return abort(404);
        }

        $invoiceDate = FacturaCabecera::find($savedQr->factura_id);

        return view('qr.scanned-page', compact('savedQr', 'invoiceDate'));
    }
}
