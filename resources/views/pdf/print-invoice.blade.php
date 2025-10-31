<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura - Ticket 80mm</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-size: 12px;
            line-height: 1;
            width: 70mm;
            max-width: 70mm;
            margin: 0 6px;
            color: #000;
            background: #fff;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .upper {
            text-transform: uppercase;
        }

        .small {
            font-size: 13px;
        }

        .large {
            font-size: 15px;
        }

        /* Logo y encabezado */
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo {
            max-height: 60px;
            max-width: 60px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0;
        }

        .ruc-info {
            font-size: 12px;
            margin-bottom: 8px;
        }

        /* Separadores */
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .separator-solid {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        /* Información del cliente */
        .client-info {
            margin-bottom: 8px;
        }

        .client-info p {
            margin: 2px 0;
            font-size: 13px;
        }

        /* Clave de acceso */
        .access-key {
            padding: 4px;
            margin: 8px 0;
        }

        .access-key-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .access-key-code {
            font-size: 11px;
            word-break: break-all;
            margin-left: 1px;
            margin-right: 2px;
        }

        /* Tabla de productos */
        .products-table {
            width: 95%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 12px;
        }

        .products-table th {
            /* CAMBIO PRINCIPAL: Removido color plomo, usado alternativas térmicas */
            color: #000;
            /* Solo negro para impresoras térmicas */
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            /* Mayor énfasis visual */
        }

        .products-table td {
            padding: 3px 2px;
            border-bottom: 1px dotted #000;
            /* Cambiado de #ccc a #000 */
            vertical-align: top;
        }

        .products-table .product-name {
            text-align: left;
            width: 45%;
            font-size: 11px;
            line-height: 1.1;
        }

        .products-table .quantity {
            text-align: center;
            width: 15%;
        }

        .products-table .price {
            text-align: right;
            width: 20%;
        }

        .products-table .total {
            text-align: right;
            width: 20%;
            font-weight: bold;
        }

        /* Totales */
        .totals {
            margin: 8px;
            padding-top: 4px;
            padding-right: 8px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            padding: 1px 0;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            padding-top: 4px;
            margin-top: 4px;
            text-transform: uppercase;
            /* Mayor énfasis */
        }

        /* QR Code */
        .qr-container {
            text-align: center;
            margin: 10px 0;
            padding-top: 8px;
        }

        .qr-code {
            max-width: 100px;
            max-height: 100px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
            line-height: 1.3;
            padding-top: 6px;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .disclaimer {
            font-style: italic;
            font-size: 12px;
        }

        .mx-2 {
            margin-left: 8px;
            margin-right: 8px;
        }

        /* Estilos adicionales para mejor compatibilidad térmica */
        .thermal-header {
            border-bottom: 2px solid #000;
            margin-bottom: 6px;
            padding-bottom: 4px;
        }

        .thermal-section {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
            padding-bottom: 4px;
        }

        /* Responsive adjustments optimizados para impresión térmica */
        @media print {
            body {
                width: 71mm;
                max-width: 71mm;
                font-size: 12px;
                -webkit-print-color-adjust: exact;
                /* Asegurar impresión de bordes negros */
                color-adjust: exact;
            }

            .separator,
            .separator-solid {
                margin: 4px 0;
            }

            /* Forzar colores negros en impresión */
            * {
                color: #000 !important;
                background: transparent !important;
            }

            /* Mantener solo bordes negros */
            .products-table th,
            .products-table td {
                border-color: #000 !important;
            }
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="data:image/png;base64,{{ $logo }}" class="logo" alt="Logo">
    </div>

    <div class="company-name center upper mx-2">
        {{ $config->nombre_comercial ?? 'Barrabas Club' }}
    </div>

    <div class="ruc-info center">
        <div class="bold">RUC: {{ $config->ruc }}</div>
        <div class="small">Dirección: {{ $config->direccion_matriz }}</div>
        <div class="small">Obligado a llevar contabilidad: {{ $config->obligado_contabilidad }}</div>
        {{-- <div class="small">Tel: {{ $config->telefono }}</div> --}}
    </div>

    <div class="center bold large upper">FACTURA</div>
    <div class="center small">No. {{ $factura ?? '001-001-000000123' }}</div>

    <br>

    <div class="client-info">
        <div class="bold">Cliente:</div>
        <div>{{ $cliente ?? 'CONSUMIDOR FINAL' }}</div>
        <div><span class="bold">Identificacion:</span> {{ $identificacion ?? '9999999999999' }} </div>
        <div style="display: flex; justify-content: space-between; margin-top: 4px;">
            <span><strong>Fecha:</strong> {{ $fecha ?? date('d/m/Y H:i') }}</span>
        </div>
    </div>

    @if(isset($claveAcceso))
        <div class="center">
            <div class="access-key-title upper">Clave de Acceso</div>
            <div class="access-key-code">{{ $claveAcceso }}</div>
        </div>
    @endif

    <table class="products-table">
        <thead>
            <tr>
                <th class="product-name">PRODUCTO</th>
                <th class="quantity">CANT</th>
                <th class="price">P.UNIT</th>
                <th class="total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="product-name">{{ $item->nombre_producto ?? 'Nombre de producto' }}</td>
                    <td class="quantity">{{ $item->cantidad ?? '1' }}</td>
                    <td class="price">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="total">${{ number_format(($item->cantidad ?? 1) * $item->precio_unitario, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="product-name">Producto de ejemplo muy largo que puede ocupar varias líneas</td>
                    <td class="quantity">2</td>
                    <td class="price">$15.50</td>
                    <td class="total">$31.00</td>
                </tr>
                <tr>
                    <td class="product-name">Otro producto</td>
                    <td class="quantity">1</td>
                    <td class="price">$8.75</td>
                    <td class="total">$8.75</td>
                </tr>
                <tr>
                    <td class="product-name">Servicio adicional</td>
                    <td class="quantity">1</td>
                    <td class="price">$5.00</td>
                    <td class="total">$5.00</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($subtotal ?? 44.75, 2) }}</span>
        </div>
        <div class="total-row">
            <span>IVA (15%):</span>
            <span>${{ number_format($iva ?? 44.75, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>${{ number_format($total ?? 51.46, 2) }}</span>
        </div>
    </div>

    @if(isset($qrCode))
        <div class="qr-container">
            <img src="{{ $qrCode }}" class="qr-code" alt="Código QR">
            <div class="small">Presente el QR al momento de ingresar</div>
        </div>
    @else
        <div class="qr-container">
            <div
                style="width: 80px; height: 80px; border: 2px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 9px;">
                QR CODE
            </div>
        </div>
    @endif

    <div class="footer">
        <div class="thank-you upper">¡Gracias por su compra!</div>
        <div class="small">Conserve este ticket como comprobante</div>
        <div class="disclaimer">Este documento no tiene validez tributaria</div>
    </div>
</body>

</html>