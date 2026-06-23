<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta #{{ $venta->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .logo-title {
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .comprobante-badge {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: right;
            border-radius: 6px;
        }
        .badge-tipo {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .bg-contado { background-color: #d1fae5; color: #065f46; }
        .bg-credito { background-color: #f3e8ff; color: #5b21b6; }
        
        .info-section {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-box {
            padding: 12px;
            vertical-align: top;
            width: 50%;
        }
        .info-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .info-content {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #1f2937;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }
        .font-mono { font-family: monospace; font-size: 11px; }
        
        .totales-table {
            width: 100%;
            border-collapse: collapse;
        }
        .credito-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px;
            color: #78350f;
            font-size: 11px;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            text-align: right;
            padding: 6px 10px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo-title">SISTEMA DE VENTAS</div>
                <div style="color: #6b7280; font-size: 11px; margin-top: 3px;">Reporte y Control de Comprobantes Oficiales</div>
            </td>
            <td style="width: 220px;">
                <div class="comprobante-badge">
                    <div class="badge-tipo {{ $venta->tipo_pago === 'contado' ? 'bg-contado' : 'bg-credito' }}">
                        Pago al {{ $venta->tipo_pago }}
                    </div>
                    <div style="font-size: 13px; font-weight: bold; color: #111827;">VENTA #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-section">
        <tr>
            <td class="info-box" style="border-right: 1px solid #edf2f7;">
                <div class="info-title">Datos del Cliente</div>
                <div class="info-content">{{ $venta->cliente->nombres }} {{ $venta->cliente->apellidos }}</div>
                <div style="margin-top: 3px; color: #4b5563;">DUI: <span class="font-mono">{{ $venta->cliente_dui }}</span></div>
                @if($venta->cliente->telefono)
                    <div style="color: #4b5563;">Teléfono: {{ $venta->cliente->telefono }}</div>
                @endif
            </td>
            <td class="info-box">
                <div class="info-title">Detalles de Emisión</div>
                <div class="info-content">Fecha: {{ date('d/m/Y', strtotime($venta->fecha_venta)) }}</div>
                <div style="margin-top: 3px; color: #6b7280;">Registrado el: {{ $venta->created_at->format('d/m/Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 50%;">Descripción del Artículo</th>
                <th style="width: 10%; text-align: center;">Cant.</th>
                <th style="width: 12%; text-align: right;">P. Unitario</th>
                <th style="width: 13%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if($venta->detalles && $venta->detalles->count() > 0)
                @foreach($venta->detalles as $detalle)
                    <tr>
                        <td class="font-mono" style="font-weight: bold; color: #4f46e5;">{{ $detalle->producto->codigo_modelo ?? 'N/A' }}</td>
                        <td style="font-weight: bold; color: #111827;">{{ $detalle->producto->descripcion ?? 'Artículo Desconocido' }}</td>
                        <td style="text-align: center;">{{ $detalle->cantidad }}</td>
                        <td style="text-align: right;">${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: #111827;">${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            @else
                @if($venta->producto)
                    <tr>
                        <td class="font-mono" style="font-weight: bold; color: #4f46e5;">{{ $venta->producto->codigo_modelo }}</td>
                        <td style="font-weight: bold; color: #111827;">{{ $venta->producto->descripcion }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">${{ number_format($venta->monto_total, 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: #111827;">${{ number_format($venta->monto_total, 2) }}</td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>

    <table class="totales-table">
        <tr>
            <td style="vertical-align: top; width: 55%; padding-right: 20px;">
                @if($venta->tipo_pago === 'credito')
                    <div class="credito-box">
                        <strong style="display:block; margin-bottom: 4px; font-size: 12px;">📋 Resumen de Crédito Activo</strong>
                        • Monto de Prima aportada: <strong>${{ number_format($venta->monto_prima, 2) }}</strong><br>
                        • Saldo Neto financiado: <strong style="color: #b91c1c;">${{ number_format($venta->monto_financiar, 2) }}</strong><br>
                        • Plan de Financiamiento: <strong>{{ $venta->numero_cuotas }} cuotas</strong> cobradas de forma <strong>{{ $venta->frecuencia_pago }}</strong>.
                    </div>
                @else
                    <div style="color: #9ca3af; font-style: italic; font-size: 11px; margin-top: 15px;">
                        * Esta venta ha sido cancelada en su totalidad al contado bajo mutuo acuerdo.
                    </div>
                @endif
            </td>
            <td style="vertical-align: top; width: 45%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: right; padding: 4px 10px; color: #6b7280; font-weight: bold; text-transform: uppercase; font-size: 10px;">Total Bruto:</td>
                        <td style="text-align: right; padding: 4px 10px; font-weight: bold; width: 100px;">${{ number_format($venta->monto_total, 2) }}</td>
                    </tr>
                    @if($venta->tipo_pago === 'credito')
                        <tr>
                            <td style="text-align: right; padding: 4px 10px; color: #6b7280; font-weight: bold; text-transform: uppercase; font-size: 10px;">(-) Prima Inicial:</td>
                            <td style="text-align: right; padding: 4px 10px; font-weight: bold; color: #059669;">${{ number_format($venta->monto_prima, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="total-row" style="text-transform: uppercase; font-size: 11px; color: #4f46e5;">Monto Final:</td>
                        <td class="total-row" style="color: #4f46e5; font-size: 16px;">
                            ${{ number_format($venta->tipo_pago === 'credito' ? $venta->monto_financiar : $venta->monto_total, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>