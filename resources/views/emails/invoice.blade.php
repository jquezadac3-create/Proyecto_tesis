<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Código QR</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
        padding: 40px;
        line-height: 1.6;
      }

      .container {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }

      h1 {
        font-size: 20px;
        margin-bottom: 20px;
        color: #2c3e50;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }

      td {
        padding: 8px 0;
        vertical-align: top;
      }

      td:first-child {
        font-weight: bold;
        width: 40%;
        color: #555;
      }

      .qr {
        text-align: center;
        margin: 20px 0;
      }

      .footer {
        margin-top: 30px;
        font-size: 14px;
        color: #777;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <p>Estimado cliente, {{ $cliente }}</p>
      <p>Gracias por realizar tu compra, te adjuntamos la correspondiente factura.</p>

      <table>
        <tr>
          <td>Numero Factura:</td>
          <td>{{ $numero_factura }}</td>
        </tr>
        <tr>
          <td>Clave de Acceso:</td>
          <td>{{ $clave_acceso }}</td>
        </tr>
      </table>

      <div class="footer">
        <p>Gracias,<br /> {{{ $config->nombre_comercial }}}</p>
      </div>
    </div>
  </body>
</html>
