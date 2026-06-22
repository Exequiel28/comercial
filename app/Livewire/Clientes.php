<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;

class Clientes extends Component
{
    // Control de estado de edición (Guardará el DUI original que estamos editando)
    public $clienteId = null; 

    // Campos del formulario
    public $dui = '';
    public $nombres = '';
    public $apellidos = '';
    public $direccion = '';
    public $telefono = '';
    public $contacto_referencia = '';

    // Reglas dinámicas: Ajustadas para validar la clave primaria 'dui'
    public function rules() 
    {
        return [
            // unique:tabla,columna,valor_a_ignorar,columna_llave_primaria
            'dui' => 'required|max:10|unique:clientes,dui,' . $this->clienteId . ',dui',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20',
            'contacto_referencia' => 'required|string',
        ];
    }

    protected $messages = [
        'dui.required' => 'El DUI es obligatorio.',
        'dui.unique' => 'Este DUI ya está registrado en el sistema.',
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'direccion.required' => 'La dirección es obligatoria.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'contacto_referencia.required' => 'La referencia es obligatoria.',
    ];

    public function guardar()
    {
        $this->validate();

        if ($this->clienteId) {
            // Modo Edición: Buscamos por la llave primaria vieja (clienteId) por si modificaron el input DUI
            $cliente = Cliente::withTrashed()->findOrFail($this->clienteId);
            $cliente->update([
                'dui' => $this->dui,
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'contacto_referencia' => $this->contacto_referencia,
            ]);
            session()->flash('message', '👤 Cliente actualizado con éxito.');
        } else {
            // Modo Creación
            Cliente::create([
                'dui' => $this->dui,
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'contacto_referencia' => $this->contacto_referencia,
            ]);
            session()->flash('message', '👤 Cliente registrado con éxito.');
        }

        $this->cancelarEdicion();
    }

    // Cargar los datos del cliente seleccionando su DUI
    public function editarCliente($dui)
    {
        $cliente = Cliente::withTrashed()->findOrFail($dui);
        
        $this->clienteId = $cliente->dui; // Almacenamos el DUI como identificador de edición
        $this->dui = $cliente->dui;
        $this->nombres = $cliente->nombres;
        $this->apellidos = $cliente->apellidos;
        $this->direccion = $cliente->direccion;
        $this->telefono = $cliente->telefono;
        $this->contacto_referencia = $cliente->contacto_referencia;
    }

    public function cancelarEdicion()
    {
        $this->reset(['clienteId', 'dui', 'nombres', 'apellidos', 'direccion', 'telefono', 'contacto_referencia']);
    }

    // Eliminar de manera lógica usando la llave primaria 'dui'
    public function eliminarCliente($dui)
    {
        $cliente = Cliente::findOrFail($dui);
        $cliente->delete(); // Llena 'deleted_at', protegiendo las llaves foráneas en las tablas relacionales

        if ($this->clienteId === $dui) {
            $this->cancelarEdicion();
        }

        session()->flash('message', "👤 El cliente {$cliente->nombres} {$cliente->apellidos} ha sido deshabilitado.");
    }

    public function render()
    {
        return view('livewire.clientes', [
            'clientes' => Cliente::withTrashed()->orderBy('nombres')->get()
        ])->layout('layouts.app');
    }
}