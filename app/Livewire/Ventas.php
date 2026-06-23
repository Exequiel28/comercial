<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; // 👈 Habilitado para la paginación del historial
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // 👈 NUEVO: Importación para generar PDF sin salir de Livewire

class Ventas extends Component
{
    use WithPagination; // 👈 Habilitamos el trait de paginación reactiva

    // Control de estado para visualización de detalles
    public $ventaSeleccionada = null; // 👈 Almacena la venta a mostrar en el modal

    // Campos del formulario de venta
    public $cliente_dui = ''; // Enlazará el DUI definitivo seleccionado
    public $fecha_venta = '';
    public $tipo_pago = 'contado';
    public $monto_total = 0;
    public $monto_prima = 0;
    public $monto_financiar = 0;
    public $frecuencia_pago = 'mensual';
    public $numero_cuotas = 0;

    // Propiedades para la barra de búsqueda interactiva
    public $searchDui = '';
    public $searchProducto = ''; // Buscador reactivo para la lista de productos

    // Arreglo dinámico para manejar el carrito multiproducto
    public $carrito = [];

    // Campos para el Modal de Registro Rápido de Cliente
    public $nuevo_dui = '';
    public $nuevo_nombres = '';
    public $nuevo_apellidos = '';
    public $nuevo_direccion = '';
    public $nuevo_telefono = '';
    public $nuevo_contacto_referencia = '';
    

    public function mount()
    {
        $this->fecha_venta = date('Y-m-d');
    }

    // Monitoreamos los cambios en el formulario en tiempo real
    public function updated($propertyName)
    {
        // Si cambia una cantidad dentro del carrito o la prima, recalculamos los montos financieros
        if (str_starts_with($propertyName, 'carrito') || $propertyName === 'monto_prima') {
            $this->calcularTotales();
        }
    }

    /**
     * Agrega un producto seleccionado mediante el buscador al carrito de compras
     */
    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);

        if (!$producto) {
            return;
        }

        // Si el producto ya existe en el carrito, incrementamos la cantidad en 1
        foreach ($this->carrito as $index => $item) {
            if ($item['id'] === $producto->id) {
                $this->carrito[$index]['cantidad']++;
                $this->searchProducto = ''; 
                $this->calcularTotales();
                return;
            }
        }

        // Si es un producto nuevo, lo agregamos con estructura base
        $this->carrito[] = [
            'id' => $producto->id,
            'descripcion' => $producto->descripcion,
            'precio' => floatval($producto->precio),
            'cantidad' => 1
        ];

        $this->searchProducto = ''; 
        $this->calcularTotales();
    }

    /**
     * Remueve un ítem específico del carrito usando su índice posicional
     */
    public function removerDelCarrito($index)
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito); // Reindexamos para evitar huecos en las llaves del array
        $this->calcularTotales();
    }

    /**
     * Calcula dinámicamente el monto total de los subtotales del carrito y define el financiamiento
     */
    private function calcularTotales()
    {
        $totalSumado = 0;

        foreach ($this->carrito as $item) {
            $amount = is_numeric($item['cantidad']) ? intval($item['cantidad']) : 0;
            $totalSumado += floatval($item['precio']) * $amount;
        }

        $this->monto_total = $totalSumado;
        $prima = is_numeric($this->monto_prima) ? floatval($this->monto_prima) : 0;
        
        // Evitamos que el monto a financiar quede en negativo
        $this->monto_financiar = max(0, $this->monto_total - $prima);
    }

    /**
     * Guarda la venta con su respectivo maestro-detalle y descuenta el stock
     */
    public function guardar()
    {
        // Reglas de validación base
        $rules = [
            'cliente_dui' => 'required|exists:clientes,dui',
            'fecha_venta' => 'required|date',
            'tipo_pago' => 'required|in:contado,credito',
            'carrito' => 'required|array|min:1',
            'carrito.*.cantidad' => 'required|integer|min:1',
        ];

        // Reglas condicionales si el pago es al crédito
        if ($this->tipo_pago === 'credito') {
            $rules['monto_prima'] = 'required|numeric|min:0|max:' . $this->monto_total;
            $rules['frecuencia_pago'] = 'required|in:diario,semanal,quincenal,mensual';
            $rules['numero_cuotas'] = 'required|integer|min:1';
        }

        $this->validate($rules, [
            'carrito.required' => 'Debes añadir por lo menos un artículo al carrito para procesar una venta.'
        ]);

        // VERIFICACIÓN PREVIA DE STOCK: Asegurar existencias de todo el carrito antes de guardar
        foreach ($this->carrito as $item) {
            $producto = Producto::find($item['id']);
            if (!$producto || $producto->stock < $item['cantidad']) {
                $this->addError('carrito', "¡Lo sentimos! No hay stock suficiente para '{$item['descripcion']}'. Stock disponible: {$producto->stock} unidades.");
                return;
            }
        }

        $estado = ($this->tipo_pago === 'contado') ? 'contado' : 'pendiente';

        // Procesamos los inserts usando una transacción SQL para garantizar integridad de datos
        DB::transaction(function () use ($estado) {
            // Guardamos el primer producto ID solo como fallback por compatibilidad histórica con tu base de datos
            $primerProductoId = count($this->carrito) > 0 ? $this->carrito[0]['id'] : null;

            // 1. Registramos el registro maestro de la Venta
            $venta = Venta::create([
                'cliente_dui' => $this->cliente_dui,
                'producto_id' => $primerProductoId, // Fallback temporal
                'fecha_venta' => $this->fecha_venta,
                'tipo_pago' => $this->tipo_pago,
                'monto_total' => $this->monto_total,
                'monto_prima' => ($this->tipo_pago === 'credito') ? $this->monto_prima : 0,
                'monto_financiar' => ($this->tipo_pago === 'credito') ? $this->monto_financiar : 0,
                'frecuencia_pago' => ($this->tipo_pago === 'credito') ? $this->frecuencia_pago : 'mensual',
                'numero_cuotas' => ($this->tipo_pago === 'credito') ? $this->numero_cuotas : 0,
                'estado_credito' => $estado,
            ]);

            // 2. Insertamos cada producto al detalle y decrementamos los inventarios individuales
            foreach ($this->carrito as $item) {
                $producto = Producto::find($item['id']);
                $producto->decrement('stock', $item['cantidad']);

                // Inserción en la tabla hija intermedia
                $venta->detalles()->create([
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad']
                ]);
            }
        });

        // Reiniciamos el formulario por completo incluyendo carritos y buscadores
        $this->reset(['cliente_dui', 'searchDui', 'searchProducto', 'carrito', 'monto_total', 'monto_prima', 'monto_financiar', 'numero_cuotas']);
        $this->tipo_pago = 'contado';
        $this->fecha_venta = date('Y-m-d');

        session()->flash('message', 'Venta procesada con éxito. El inventario ha sido actualizado.');
    }

    // Método para registrar un cliente de forma rápida desde el modal de la venta
    public function guardarClienteRapido()
    {
        $this->validate([
            'nuevo_dui' => 'required|unique:clientes,dui',
            'nuevo_nombres' => 'required|string|max:255',
            'nuevo_apellidos' => 'required|string|max:255',
            'nuevo_direccion' => 'required|string|max:500',
            'nuevo_telefono' => 'nullable|string|max:20',
            'nuevo_contacto_referencia' => 'nullable|string|max:255',
        ], [
            'nuevo_dui.required' => 'El DUI es obligatorio.',
            'nuevo_dui.unique' => 'Este número de DUI ya se encuentra registrado.',
            'nuevo_nombres.required' => 'Los nombres son obligatorios.',
            'nuevo_apellidos.required' => 'Los apellidos son obligatorios.',
            'nuevo_direccion.required' => 'La dirección es obligatoria.',
        ]);

        $nuevoCliente = Cliente::create([
            'dui' => $this->nuevo_dui,
            'nombres' => $this->nuevo_nombres,
            'apellidos' => $this->nuevo_apellidos,
            'direccion' => $this->nuevo_direccion,
            'telefono' => $this->nuevo_telefono,
            'contacto_referencia' => $this->nuevo_contacto_referencia,
        ]);

        $this->cliente_dui = $nuevoCliente->dui;

        $this->reset([
            'nuevo_dui', 'nuevo_nombres', 'nuevo_apellidos', 'nuevo_direccion', 'nuevo_telefono', 'nuevo_contacto_referencia'
        ]);

        $this->dispatch('close-modal', name: 'modal-cliente-rapido'); 

        session()->flash('message', '✨ Cliente registrado y seleccionado correctamente para la venta.');
    }

    /**
     * Carga la venta seleccionada para mostrarla en el modal de detalle
     */
    public function verDetalleVenta($ventaId)
    {
        $this->ventaSeleccionada = null;

        $this->ventaSeleccionada = Venta::with(['cliente', 'detalles.producto', 'producto'])
            ->findOrFail($ventaId);

        // 🛠️ INTEGRADO: Ordenamos a Flux UI que despliegue el modal tras cargar los datos
        $this->js('$flux.modal("modal-detalle-venta").show()');
    }

    /**
     * ➕ Procesa y descarga en caliente el PDF usando DomPDF desde Livewire
     */
    public function descargarPdf($ventaId)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'producto'])->findOrFail($ventaId);

        // Renderizamos el HTML limpio diseñado en tu carpeta tradicional
        $pdf = Pdf::loadView('pdf.comprobante_venta', compact('venta'));

        // streamDownload procesa la salida binaria sin requerir redirección web externa
        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            "comprobante_venta_{$ventaId}.pdf"
        );
    }

    public function render()
    {
        // 1. Filtrado reactivo de Clientes
        $clientesFiltrados = Cliente::query()
            ->when($this->searchDui, function($query) {
                $query->where('dui', 'like', '%' . $this->searchDui . '%')
                      ->orWhere('nombres', 'like', '%' . $this->searchDui . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->searchDui . '%');
            })
            ->orderBy('nombres')
            ->take(10)
            ->get();

        // 2. Buscador Reactivo de Productos filtrado por texto e inventario activo
        $productosFiltrados = [];
        if (!empty($this->searchProducto)) {
            $productosFiltrados = Producto::query()
                ->where('stock', '>', 0)
                ->where(function($query) {
                    $query->where('descripcion', 'like', '%' . $this->searchProducto . '%')
                          ->orWhere('codigo_modelo', 'like', '%' . $this->searchProducto . '%')
                          ->orWhere('marca', 'like', '%' . $this->searchProducto . '%');
                })
                ->orderBy('descripcion')
                ->take(10)
                ->get();
        }

        return view('livewire.ventas', [
            'clientes' => $clientesFiltrados,
            'productosBusqueda' => $productosFiltrados,
            'ventas' => Venta::with(['cliente', 'detalles.producto'])
                ->orderBy('created_at', 'desc')
                ->paginate(5)
        ])->layout('layouts.app');
    }
}