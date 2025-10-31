<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        .my-0 {
            margin-top: 0;
            margin-bottom: 0;
        }

        .wrapper {
            padding: 10px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .col {
            width: 48%;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            height: 120px;
        }

        .box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .normal {
            font-weight: normal;
        }

        .upper {
            text-transform: uppercase;
        }

        .mt {
            margin-top: 5px;
        }

        .mb {
            margin-bottom: 5px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .small {
            font-size: 10px;
            word-break: break-all;
        }

        .barcode {
            width: 100%;
        }

        .right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }

        .spacer {
            height: 10px;
        }

        .twocol {
            width: 100%;
            table-layout: fixed;
            margin-bottom: 20px;
        }

        .left-col,
        .right-col {
            vertical-align: bottom;
            padding: 0 5px;
        }

        .left-col {
            width: 50%;
        }

        .right-col {
            width: 50%;
        }
    </style>
</head>

<body>
    @php
        $infoTr = $invoice->comprobante->factura->infoTributaria;
        $infoFc = $invoice->comprobante->factura->infoFactura;
        $detalles = $invoice->comprobante->factura->detalles;
        $infoAd = $invoice->comprobante->factura->infoAdicional;
    @endphp
    <div class="wrapper">
        <table class="twocol">
            <tr>
                <td class="left-col">
                    <div class="logo-container">
                        <img src="{{ $logo }}" alt="logo" class="logo">
                    </div>
                    <div class="box">
                        <p class="bold">{{ $infoTr->nombreComercial }}</p>
                        <p class="mt"><span class="bold">Matriz:</span>
                            {{ $infoTr->dirMatriz }}</p>
                        <p><span class="bold">Sucursal:</span>
                            {{ $infoFc->dirEstablecimiento }}</p>

                        <!-- Obtener de DB -->
                        {{-- <p><span class="bold">Teléfono:</span> 0999123456</p>
                        <p><span class="bold">E-mail:</span> info@empresa.com</p> --}}

                        <p class="bold">Obligado a llevar contabilidad: <span
                                class="normal upper">{{ $infoFc->obligadoContabilidad }}</span>
                        </p>
                    </div>
                </td>

                <td class="right-col">
                    <div class="box">
                        <p><span class="bold">R.U.C.: </span>{{ $infoTr->ruc }}
                        </p>
                        <p class="mt title">FACTURA</p>
                        @php
                            $invoiceNumber = "{$infoTr->estab} - {$infoTr->ptoEmi} - {$infoTr->secuencial}";

                            $unformattedDate = $invoice->fechaAutorizacion;
                            $formattedDate = \Carbon\Carbon::parse($unformattedDate)->format('d/m/Y H:i:s');
                        @endphp
                        <p><span class="bold">No.:</span> {{ $invoiceNumber }}</p>
                        <p class="mt bold">Número de autorización:</p>
                        <p class="small">{{ $infoTr->claveAcceso }}</p>
                        <p class="mt bold">Fecha y hora de autorización:</p>
                        <p>{{ $formattedDate }}</p>
                        <p class="mt bold">Ambiente: <span class="normal upper">{{ $invoice->ambiente }}</span></p>
                        <p class="bold">Emisión: <span
                                class="normal">{{ $infoTr->tipoEmision == 1 ? 'NORMAL' : 'CONTINGENCIA' }}</span></p>
                        <p class="mt">CLAVE DE ACCESO</p>
                        <img class="barcode" src="{{ $barcode }}" alt="Código de barras" />
                        <p class="small center my-0">{{ $infoTr->claveAcceso }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="twocol box">
            <tr>
                <td>
                    <p><span class="bold">Razón Social / Nombres y Apellidos: </span>
                        {{ $infoFc->razonSocialComprador }} </p>

                </td>
            </tr>
            <tr>
                <td>
                    <p><span class="bold">Identificación: </span> {{ $infoFc->identificacionComprador }}</p>
                </td>
                <td class="col">
                    <p><span class="bold">Fecha Emisión: </span>{{ $infoFc->fechaEmision }}</p>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Cod. Principal</th>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                    <th>Precio Unitario</th>
                    <th>Descuento</th>
                    <th>Precio Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($detalles->detalle as $detalle)
                    <tr>
                        <td>{{ $detalle->codigoPrincipal }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->descripcion }}</td>
                        <td class="right">{{ $detalle->precioUnitario }}</td>
                        <td class="right">{{ $detalle->descuento }}</td>
                        <td class="right">{{ $detalle->precioTotalSinImpuesto }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay detalles disponibles</td>
                    </tr>
                @endforelse

                <tr>
                    <td colspan="6" class="spacer no-border"></td>
                </tr>

                @php
                    $infoImpuestos = $infoFc->totalConImpuestos->totalImpuesto;
                    $array = [];

                    foreach ($infoImpuestos as $impuesto) {
                        $codigo = (string) $impuesto->codigoPorcentaje;

                        if (!isset($array[$codigo])) {
                            $array[$codigo] = [
                                'valor' => 0,
                                'baseImp' => 0,
                                'descuento' => 0,
                            ];
                        }

                        $array[$codigo]['valor'] += (float) $impuesto->valor;
                        $array[$codigo]['baseImp'] += (float) $impuesto->baseImponible;

                        if (isset($impuesto->descuentoAdicional)) {
                            $array[$codigo]['descuento'] += (float) $impuesto->descuentoAdicional;
                        }
                    }

                @endphp
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">Subtotal 15%</td>
                    <td class="right">{{ $array[4]['baseImp'] ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">Subtotal 5%</td>
                    <td class="right">{{ $array[5]['baseImp'] ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">Subtotal 0%</td>
                    <td class="right">{{ $array[0]['baseImp'] ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">Descuento</td>
                    <td class="right">{{ $array['descuento'] ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">IVA 15%</td>
                    <td class="right">{{ $array[4]['valor'] ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">IVA 5%</td>
                    <td class="right">{{ $array[5]['valor'] ?? 0.00 }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">ICE</td>
                    <td class="right">1.00</td>
                </tr> --}}
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">ADICIONAL</td>
                    <td class="right">{{ $infoFc->adicional ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td colspan="2" class="bold">VALOR TOTAL</td>
                    <td class="right"> {{ round(floatval($infoFc->importeTotal), 2) }} </td>
                </tr>
                <tr>
                    <td colspan="6" class="spacer no-border"></td>
                </tr>
                <tr>
                    <td colspan="3" class="bold">Forma de Pago</td>
                    <td colspan="2" class="bold">Valor</td>
                    <td class="no-border"></td>
                </tr>

                @foreach ($infoFc->pagos->pago as $pago)
                    <tr>
                        <td colspan="3">{{ \App\Enums\FormaPago::fromCodigo($pago->formaPago)->label() }}</td>
                        <td colspan="2" class="right"> {{ round(floatval($pago->total), 2) }} </td>
                        <td class="no-border"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>