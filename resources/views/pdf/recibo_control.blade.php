<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Control de Abono</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
            color: #1e293b;
        }
        .header .receipt-number {
            font-size: 14px;
            font-family: monospace;
            color: #4f46e5;
            font-weight: bold;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            text-transform: uppercase;
            color: #4a5568;
            font-size: 10px;
            width: 15%;
        }
        .value {
            color: #1a202c;
            border-bottom: 1px dashed #cbd5e1;
            padding-left: 5px;
        }
        .amount-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .amount-box .title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #166534;
            margin-bottom: 2px;
        }
        .amount-box .price {
            font-size: 22px;
            font-weight: 900;
            color: #15803d;
        }
        .control-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .control-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            padding: 8px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .control-table td {
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-size: 12px;
        }
        .control-table .bg-highlight {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .footer-text {
            text-align: center;
            font-style: italic;
            color: #718096;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    
    <div class="header">
        <h1>Recibo de Control</h1>
        <div class="receipt-number">RECIBO N°: #{{ str_pad($abono_id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="amount-box">
        <div class="title">Por $</div>
        <div class="price">${{ number_format($monto_abono, 2) }}</div>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">Señor:</td>
            <td class="value" colspan="3" style="font-weight: bold; font-size: 13px;">{{ $cliente_nombre }}</td>
        </tr>
        <tr>
            <td class="label">DUI:</td>
            <td class="value" style="width: 35%;">{{ $cliente_dui }}</td>
            <td class="label" style="width: 15%; padding-left: 15px;">Producto:</td>
            <td class="value">{{ $producto_descripcion }}</td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td class="value" colspan="3">{{ $cliente_direccion ?? 'Dirección registrada en sistema' }}</td>
        </tr>
        <tr>
            <td class="label">Vendedor:</td>
            <td class="value" colspan="3">{{ $vendedor_nombre }}</td>
        </tr>
        <tr>
            <td class="label">Concepto:</td>
            <td class="value" colspan="3" style="color: #4a5568; font-style: italic;">{{ $nota ?? 'Abono regular a cuenta' }}</td>
        </tr>
    </table>

    <table class="control-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Saldo Anterior</th>
                <th>Abono</th>
                <th>Resta</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ date('d/m/Y', strtotime($fecha_abono)) }}</td>
                <td class="text-gray-600">${{ number_format($saldo_anterior, 2) }}</td>
                <td class="bg-highlight text-emerald-700">+${{ number_format($monto_abono, 2) }}</td>
                <td class="bg-highlight" style="color: #b91c1c; font-size: 13px;">${{ number_format($saldo_restante, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-text">
        Este documento es un comprobante oficial de su pago. Por favor consérvelo para su control financiero.
    </div>

</div>

</body>
</html>