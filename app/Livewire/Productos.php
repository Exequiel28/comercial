<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; // 👈 Importamos el trait de paginación
use App\Models\Producto;

class Productos extends Component
{
    use WithPagination; // 👈 Habilitamos el trait de paginación reactiva

    // Campos del formulario principal
    public $productoId = null; // Para saber si estamos editando
    public $codigo_modelo = '';
    public $descripcion = '';
    public $marca = '';
    public $precio = '';
    public $stock = '';

    // Propiedades para la carga variable de stock
    public $productoStockId = null;
    public $cantidad_a_sumar = '';
    public $nombre_producto_stock = '';

    // Modificamos las reglas dinámicamente para la edición
    public function rules() 
    {
        return [
            // Usamos withTrashed() para que valide correctamente el código único incluso si está deshabilitado
            'codigo_modelo' => 'required|max:50|unique:productos,codigo_modelo,' . $this->productoId,
            'descripcion' => 'required|string|max:255',
            'marca' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ];
    }

    protected $messages = [
        'codigo_modelo.required' => 'El código o modelo es obligatorio.',
        'codigo_modelo.unique' => 'Este código de modelo ya existe en el inventario.',
        'descripcion.required' => 'La descripción del producto es obligatoria.',
        'marca.required' => 'La marca es obligatoria.',
        'precio.required' => 'El precio de venta es obligatorio.',
        'precio.numeric' => 'El precio debe ser un número válido.',
        'stock.required' => 'La cantidad en stock es obligatoria.',
        'stock.integer' => 'El stock debe ser un número entero.',
    ];

    public function guardar()
    {
        $this->validate();

        if ($this->productoId) {
            // Modo Edición (Buscamos con withTrashed por si editamos uno deshabilitado)
            $producto = Producto::withTrashed()->findOrFail($this->productoId);
            $producto->update([
                'codigo_modelo' => strtoupper($this->codigo_modelo),
                'descripcion' => $this->descripcion,
                'marca' => $this->marca,
                'precio' => $this->precio,
                'stock' => $this->stock,
            ]);
            session()->flash('message', '📦 Producto actualizado en el inventario con éxito.');
        } else {
            // Modo Creación
            Producto::create([
                'codigo_modelo' => strtoupper($this->codigo_modelo),
                'descripcion' => $this->descripcion,
                'marca' => $this->marca,
                'precio' => $this->precio,
                'stock' => $this->stock,
            ]);
            session()->flash('message', '📦 Producto agregado al inventario con éxito.');
        }

        // Limpiamos los campos y regresamos al estado inicial
        $this->cancelarEdicion();
    }

    // Cargar datos en el formulario para editar
    public function editarProducto($id)
    {
        $producto = Producto::withTrashed()->findOrFail($id);
        
        $this->productoId = $producto->id;
        $this->codigo_modelo = $producto->codigo_modelo;
        $this->descripcion = $producto->descripcion;
        $this->marca = $producto->marca;
        $this->precio = $producto->precio;
        $this->stock = $producto->stock;
    }

    // Cancelar la edición actual
    public function cancelarEdicion()
    {
        $this->reset(['productoId', 'codigo_modelo', 'descripcion', 'marca', 'precio', 'stock']);
    }

    // Abrir el modal de stock y precargar datos elementales del artículo
    public function abrirModalStock($id)
    {
        $producto = Producto::withTrashed()->findOrFail($id);
        $this->productoStockId = $producto->id;
        $this->nombre_producto_stock = $producto->codigo_modelo . ' - ' . $producto->descripcion;
        $this->cantidad_a_sumar = ''; // Reseteamos el input del modal
        
        // Ejecuta el helper global de Alpine para abrir el modal de Flux
        $this->js("\$flux.modal('modal-add-stock').show()");
    }

    // Procesar la suma exacta configurada en el modal
    public function guardarStockRapido()
    {
        $this->validate([
            'cantidad_a_sumar' => 'required|integer|min:1'
        ], [
            'cantidad_a_sumar.required' => 'La cantidad es obligatoria.',
            'cantidad_a_sumar.integer' => 'Debe ingresar un valor numérico entero.',
            'cantidad_a_sumar.min' => 'Debes ingresar al menos 1 unidad.'
        ]);

        $producto = Producto::withTrashed()->findOrFail($this->productoStockId);
        $producto->increment('stock', $this->cantidad_a_sumar);

        session()->flash('message', "✨ Se añadieron {$this->cantidad_a_sumar} unidades al stock de {$producto->codigo_modelo}.");

        // Cerramos el modal limpiamente y reseteamos variables temporales
        $this->js("\$flux.modal('modal-add-stock').close()");
        $this->reset(['productoStockId', 'cantidad_a_sumar', 'nombre_producto_stock']);
    }

    // Método para alternar el estado del producto de forma reactiva (Toggle Switch)
    public function toggleEstado($id)
    {
        $producto = Producto::withTrashed()->findOrFail($id);

        if ($producto->trashed()) {
            // Si está deshabilitado (borrado lógico), lo restauramos
            $producto->restore();
            session()->flash('message', "🟢 El producto {$producto->codigo_modelo} ha sido habilitado con éxito.");
        } else {
            // Si está habilitado, lo deshabilitamos (borrado lógico)
            $producto->delete();
            session()->flash('message', "🔴 El producto {$producto->codigo_modelo} ha sido deshabilitado.");
        }
    }

    public function render()
    {
        return view('livewire.productos', [
            // 👈 Modificado: Cambiado ->get() por ->paginate(10) para un rendimiento veloz del inventario
            'productos' => Producto::withTrashed()->orderBy('descripcion')->paginate(10) 
        ])->layout('layouts.app');
    }
}