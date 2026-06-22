<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Completo de Crédito</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #334155; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0; color: #1e293b; text-transform: uppercase; }
        .info-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .info-box td { padding: 8px; border: 1px solid #e2e8f0; }
        .label { font-weight: bold; text-transform: uppercase; color: #475569; font-size: 10px; }
        .table-historial { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-historial th { background-color: #334155; color: white; padding: 8px; font-size: 10px; text-transform: uppercase; text-align: center; }
        .table-historial td { padding: 8px; border: 1px solid #cbd5e1; text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .status-badge { font-size: 14px; font-weight: bold; color: #b91c1c; text-align: center; margin-top: 20px; padding: 10px; background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: 6px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Estado e Historial de Crédito</h1>
        <div style="margin-top: 4px; font-weight: bold; color: #475569;">
            Referencia Interna: Venta #{{ $venta_id }}
        </div>
        <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
            Reporte generado el: {{ $fecha_emision }}
        </div>
    </div>

    <table class="info-box">
        <tr>
            <td class="label" style="width: 20%;">Cliente:</td>
            <td class="text-bold" style="width: 40%;">{{ $cliente_nombre }}</td>
            <td class="label" style="width: 20%;">DUI Titular:</td>
            <td style="width: 20%;">{{ $cliente_dui }}</td>
        </tr>
        <tr>
            <td class="label">Productos Financiados:</td>
            <!-- Usamos nl2br para que cada artículo salga en su propia línea limpiamente -->
            <td class="text-bold" colspan="3" style="text-align: left; font-size: 10px; color: #1e293b; line-height: 1.5; white-space: pre-line;">
                {!! nl2br(e($producto_descripcion)) !!}
            </td>
        </tr>
        <tr>
            <td class="label">Total de la Venta:</td>
            <td class="text-bold">${{ number_format($monto_total, 2) }}</td>
            <td class="label" style="color: #047857;">Prima Entregada:</td>
            <td class="text-bold" style="color: #047857;">-${{ number_format($monto_prima, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Inicial Financiado:</td>
            <td class="text-bold" style="color: #1e3a8a;">${{ number_format($monto_financiar, 2) }}</td>
            <td class="label" style="color: #b91c1c;">Saldo Restante Hoy:</td>
            <td class="text-bold" style="color: #b91c1c;">${{ number_format($saldo_final_actual, 2) }}</td>
        </tr>
    </table>

    <h3>Listado Cronológico de Abonos Registrados</h3>

    <table class="table-historial">
        <thead>
            <tr>
                <th>N° Recibo</th>
                <th>Fecha Pago</th>
                <th>Saldo Anterior</th>
                <th>Monto Abonado</th>
                <th>Resta Pendiente</th>
                <th>Concepto / Nota</th>
            </tr>
        </thead>
        <tbody>
            @if(count($historial) == 0)
                <tr>
                    <td colspan="6" style="color: #64748b; font-style: italic; padding: 15px;">No se registran movimientos ni primas asociadas a este crédito aún.</td>
                </tr>
            @else
                @foreach($historial as $item)
                    <tr>
                        <td class="text-bold" style="font-family: monospace;">#{{ str_pad($item['id'], 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ date('d/m/Y', strtotime($item['fecha'])) }}</td>
                        <td>${{ number_format($item['saldo_anterior'], 2) }}</td>
                        <td class="text-bold" style="color: #16a34a;">${{ number_format($item['monto_abono'], 2) }}</td>
                        <td class="text-bold" style="color: #b91c1c;">${{ number_format($item['resta'], 2) }}</td>
                        <td style="text-align: left; font-style: italic; color: #475569;">{{ $item['nota'] ?? 'Abono regular procesado' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="status-badge">
        SALDO TOTAL QUE RESTA POR CANCELAR: ${{ number_format($saldo_final_actual, 2) }}
    </div>

</body>
</html>