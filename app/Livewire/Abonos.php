<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Abono;
use Barryvdh\DomPDF\Facade\Pdf;

class Abonos extends Component
{
    // Campos de búsqueda y filtrado comercial
    public $searchDui = '';
    public $venta_id = '';

    // Campos del formulario de abono
    public $fecha_abono = '';
    public $monto_abono = '';
    public $nota = '';

    // Propiedades para mostrar información del crédito seleccionado
    public $detalles_credito = null;

    public function mount()
    {
        $this->fecha_abono = date('Y-m-d'); // Fecha de hoy por defecto
    }

    // Si el usuario modifica el texto del DUI, reseteamos el crédito seleccionado para evitar errores cruzados
    public function updatedSearchDui()
    {
        $this->reset(['venta_id', 'detalles_credito']);
    }

    // Monitoreamos cuando el usuario selecciona un crédito específico de la lista filtrada
    public function updatedVentaId($id)
    {
        if ($id) {
            $venta = Venta::with(['cliente', 'abonos', 'producto'])->find($id);
            if ($venta) {
                $total_abonado = $venta->abonos->sum('monto_abono');
                $saldo_pendiente = $venta->monto_financiar - $total_abonado;
                
                $cuota_calculada = ($venta->numero_cuotas > 0) ? ($venta->monto_financiar / $venta->numero_cuotas) : 0;

                $this->detalles_credito = [
                    'cliente' => $venta->cliente->nombres . ' ' . $venta->cliente->apellidos,
                    'monto_total' => $venta->monto_total,      // 🔴 Inyectado para la vista
                    'monto_prima' => $venta->monto_prima,      // 🔴 Inyectado para la vista
                    'monto_financiar' => $venta->monto_financiar,
                    'total_abonado' => $total_abonado,
                    'saldo_pendiente' => $saldo_pendiente,
                    'cuotas_pactadas' => $venta->numero_cuotas . ' cuotas ' . $venta->frecuencia_pago . 's',
                    'cuota_calculada' => $cuota_calculada
                ];
                return;
            }
        }
        $this->detalles_credito = null;
    }

    public function guardar()
    {
        $this->validate([
            'venta_id' => 'required|exists:ventas,id',
            'fecha_abono' => 'required|date',
            'monto_abono' => 'required|numeric|min:0.01',
            'nota' => 'nullable|string|max:255',
        ]);

        $venta = Venta::with(['cliente', 'abonos', 'producto'])->find($this->venta_id);
        $total_abonado = $venta->abonos->sum('monto_abono');
        $saldo_anterior = $venta->monto_financiar - $total_abonado; // Corresponde al Saldo Anterior antes del nuevo abono

        if ($this->monto_abono > $saldo_anterior) {
            $this->addError('monto_abono', 'El monto del abono ($' . $this->monto_abono . ') no puede ser mayor al saldo pendiente ($' . number_format($saldo_anterior, 2) . ').');
            return;
        }

        // 1. Registramos el abono en la Base de Datos
        $nuevoAbono = Abono::create([
            'venta_id' => $this->venta_id,
            'fecha_abono' => $this->fecha_abono,
            'monto_abono' => $this->monto_abono,
            'nota' => $this->nota,
        ]);

        $saldo_restante = $saldo_anterior - $this->monto_abono; // La 'Resta' final

        if ($saldo_restante <= 0) {
            $venta->update(['estado_credito' => 'pagado']);
        }

        // 2. Mapeamos y preparamos los datos requeridos por tu "Recibo de Control" físico
        $dataPdf = [
            'abono_id' => $nuevoAbono->id,
            'monto_abono' => $this->monto_abono,
            'cliente_nombre' => $venta->cliente->nombres . ' ' . $venta->cliente->apellidos,
            'cliente_dui' => $venta->cliente_dui ?? $venta->cliente->dui,
            'cliente_direccion' => $venta->cliente->direccion ?? 'San Salvador, El Salvador',
            'producto_descripcion' => $venta->producto->descripcion ?? 'Artículo Comercial',
            'vendedor_nombre' => auth()->user()->name ?? 'Cobrador General', 
            'nota' => $this->nota,
            'fecha_abono' => $this->fecha_abono,
            'saldo_anterior' => $saldo_anterior,
            'saldo_restante' => $saldo_restante
        ];

        // 3. Limpiamos el formulario manteniendo la consistencia de la UI
        $this->reset(['venta_id', 'monto_abono', 'nota', 'detalles_credito']);
        $this->fecha_abono = date('Y-m-d');

        session()->flash('message', '¡Abono procesado y registrado con éxito! Descargando recibo...');

        // 4. Gatillamos la descarga del flujo PDF directo al navegador/móvil
        return response()->streamDownload(function () use ($dataPdf) {
            echo Pdf::loadView('pdf.recibo_control', $dataPdf)
                ->setPaper([0, 0, 450, 550]) // Tamaño personalizado ideal para impresoras térmicas o media carta
                ->output();
        }, 'Recibo_Abono_#' . str_pad($dataPdf['abono_id'], 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function descargarHistorial()
    {
        if (!$this->venta_id) return;

        // Buscamos la venta cargando el historial de abonos y TODOS los productos desde su tabla intermedia
        $venta = Venta::with(['cliente', 'detalles.producto', 'abonos' => function($query) {
            $query->orderBy('fecha_abono', 'asc')->orderBy('id', 'asc');
        }])->find($this->venta_id);

        if (!$venta) return;

        // 🚀 CONCATENAMOS TODOS LOS PRODUCTOS DE LA VENTA:
        $productosConcatenados = $venta->detalles && $venta->detalles->count() > 0
            ? $venta->detalles->map(function($detalle) {
                $nombreProducto = $detalle->producto->descripcion ?? 'Artículo';
                return "• " . $nombreProducto . " (Cant: " . $detalle->cantidad . ")";
            })->implode("\n") // Los unimos con saltos de línea para el PDF
            : 'Artículo Comercial';

        // Procesamos el historial calculando de forma dinámica los saldos línea por línea
        $historialSaldos = [];
        $saldoAcumulado = $venta->monto_financiar;

        foreach ($venta->abonos as $abono) {
            $saldoAnterior = $saldoAcumulado;
            $saldoAcumulado -= $abono->monto_abono;

            $historialSaldos[] = [
                'id' => $abono->id,
                'fecha' => $abono->fecha_abono,
                'monto_abono' => $abono->monto_abono,
                'saldo_anterior' => $saldoAnterior,
                'resta' => $saldoAcumulado,
                'nota' => $abono->nota
            ];
        }

        $dataHistorial = [
            'venta_id' => $venta->id,
            'monto_total' => $venta->monto_total,
            'monto_prima' => $venta->monto_prima,
            'monto_financiar' => $venta->monto_financiar,
            'cliente_nombre' => $venta->cliente->nombres . ' ' . $venta->cliente->apellidos,
            'cliente_dui' => $venta->cliente_dui ?? $venta->cliente->dui,
            'producto_descripcion' => $productosConcatenados, // 👈 Enviamos la lista formateada
            'historial' => $historialSaldos,
            'saldo_final_actual' => $saldoAcumulado,
            'fecha_emision' => date('d/m/Y h:i A') 
        ];

        // Ejecuta la descarga en stream
        return response()->streamDownload(function () use ($dataHistorial) {
            echo Pdf::loadView('pdf.historial_credito', $dataHistorial)
                ->setPaper('letter', 'portrait')
                ->output();
        }, 'Historial_Credito_Venta_#' . $venta->id . '.pdf');
    }

    public function render()
    {
        $clienteEncontrado = null;
        $creditosDelCliente = collect();

        if (!empty($this->searchDui)) {
            $clienteEncontrado = Cliente::where('dui', $this->searchDui)->first();
            
            if ($clienteEncontrado) {
                $creditosDelCliente = Venta::with(['abonos', 'producto'])
                    ->where('cliente_dui', $clienteEncontrado->dui)
                    ->where('tipo_pago', 'credito')
                    ->where('estado_credito', 'pendiente')
                    ->get();
            }
        }

        return view('livewire.abonos', [
            'clienteEncontrado' => $clienteEncontrado,
            'creditos_activos' => $creditosDelCliente,
            'ultimos_abonos' => Abono::with(['venta.cliente', 'venta.producto'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
        ])->layout('layouts.app');
    }
}