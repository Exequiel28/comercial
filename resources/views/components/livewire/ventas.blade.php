<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session()->has('message'))
            <div class="mb-6 p-4 text-sm text-green-900 bg-green-200 border-l-4 border-green-600 rounded-r-lg font-bold shadow-md">
                ✨ {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 h-fit">
                <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                    <span class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">🛒</span>
                    <h3 class="text-xl font-black text-gray-800">Nueva Venta / Crédito</h3>
                </div>
                
                <form wire:submit.prevent="guardar" class="space-y-4">
                    
                    <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-700">🔍 Buscador de Clientes</p>
                            <button type="button" x-on:click="$flux.modal('modal-cliente-rapido').show()" 
                                    class="text-[11px] bg-indigo-600 text-white font-bold px-2 py-0.5 rounded shadow hover:bg-indigo-700 transition cursor-pointer">
                                ➕ Registro Rápido
                            </button>
                        </div>
                        
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Escriba DUI o Nombre</label>
                            <flux:input wire:model.live="searchDui" icon="magnifying-glass" placeholder="Ej: 00000000-0 o Juan Pérez..." />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Seleccionar Resultado</label>
                            <select wire:model="cliente_dui" 
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 font-bold shadow-sm focus:border-indigo-600 h-10 sm:text-sm">
                                <option value="">-- {{ $searchDui ? 'Resultados encontrados' : 'Escriba arriba para buscar' }} --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->dui }}">{{ $cliente->dui }} | {{ $cliente->nombres }} {{ $cliente->apellidos }}</option>
                                @endforeach
                            </select>
                            @error('cliente_dui') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: true }" @click.away="open = false">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">🔍 Añadir Artículo al Carrito</label>
                        <flux:input wire:model.live="searchProducto" @focus="open = true" icon="magnifying-glass" placeholder="Buscar por descripción, marca o código..." />
                        
                        @if(!empty($searchProducto) && count($productosBusqueda) > 0)
                            <div x-show="open" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100">
                                @foreach($productosBusqueda as $prod)
                                    <button type="button" wire:click="agregarAlCarrito({{ $prod->id }})" @click="open = false"
                                        class="w-full text-left px-4 py-2.5 text-xs font-medium hover:bg-indigo-50 text-gray-700 flex justify-between items-center transition-colors">
                                        <div>
                                            <span class="font-bold text-gray-900 block">{{ $prod->descripcion }}</span>
                                            <span class="text-gray-400 text-[10px]">Marca: {{ $prod->marca }} | Stock: <strong class="text-indigo-600">{{ $prod->stock }}</strong></span>
                                        </div>
                                        <span class="font-black text-indigo-700 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">${{ number_format($prod->precio, 2) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @elseif(!empty($searchProducto))
                            <div x-show="open" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg p-3 text-xs text-gray-400 italic shadow-md text-center">
                                ❌ No se encontraron productos con stock disponible.
                            </div>
                        @endif
                    </div>

                    <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/50">
    <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2 border-b pb-1 flex justify-between items-center">
        <span>📦 Artículos a vender</span>
        <span class="bg-gray-200 text-gray-700 font-bold px-1.5 py-0.5 rounded text-[9px]">{{ count($carrito) }} ítems</span>
    </p>

    @if(count($carrito) === 0)
        <p class="text-xs text-gray-400 italic text-center py-4">El carrito está vacío. Utiliza el buscador de arriba.</p>
    @else
        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
            @foreach($carrito as $index => $item)
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-xs space-y-2">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-gray-900 truncate max-w-[180px]">{{ $item['descripcion'] }}</p>
                        <button type="button" wire:click="removerDelCarrito({{ $index }})" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer">
                            🗑️
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-100">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Precio Unit. ($)</label>
                            <input type="number" step="0.01" wire:model.live="carrito.{{ $index }}.precio" 
                                class="w-full text-left rounded-md border-gray-300 bg-white text-gray-900 p-1 font-black focus:ring-indigo-500 focus:border-indigo-500 h-8 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Cantidad</label>
                            <input type="number" wire:model.live="carrito.{{ $index }}.cantidad" min="1" 
                                class="w-full text-center rounded-md border-gray-300 bg-gray-50 text-gray-900 p-1 font-bold focus:ring-indigo-500 focus:border-indigo-500 h-8 text-xs">
                        </div>
                    </div>

                    <div class="text-right text-[11px] text-gray-500 font-medium">
                        Subtotal: <span class="font-bold text-gray-900">${{ number_format(($item['precio'] * $item['cantidad']), 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    @error('carrito') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
</div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Fecha</label>
                            <input type="date" wire:model="fecha_venta" 
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-medium shadow-sm focus:border-indigo-600 h-10 sm:text-sm">
                            @error('fecha_venta') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Tipo de Pago</label>
                            <select wire:model.live="tipo_pago" 
                                class="mt-1 block w-full rounded-lg border-indigo-400 bg-indigo-50 font-bold text-indigo-900 shadow-sm focus:border-indigo-600 h-10 sm:text-sm">
                                <option value="contado">💰 Contado</option>
                                <option value="credito">📅 Crédito</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Monto Total de la Venta ($)</label>
                        <div class="mt-1 block w-full p-3 bg-gray-100 rounded-lg border border-gray-300 text-lg font-black text-gray-900 shadow-inner text-center">
                            $ {{ number_format($monto_total, 2) }}
                        </div>
                    </div>

                    @if($tipo_pago === 'credito')
                        <div class="bg-amber-50/70 p-4 rounded-xl border-2 border-amber-200 space-y-4 shadow-sm animate-fade-in">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800 border-b border-amber-200 pb-1">⚙️ Parámetros del Crédito</p>
                            
                            <div>
                                <label class="block text-xs font-bold uppercase text-amber-900 mb-1">Monto de Prima ($)</label>
                                <input type="number" step="0.01" wire:model.live="monto_prima" placeholder="0.00"
                                    class="mt-1 block w-full rounded-lg border-amber-300 bg-white font-bold text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 h-10 sm:text-sm">
                                @error('monto_prima') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-amber-800 mb-1">Saldo Neto a Financiar</label>
                                <div class="mt-1 block w-full p-2.5 bg-white rounded-lg border border-amber-300 text-base font-black text-amber-900 shadow-inner">
                                    $ {{ number_format($monto_financiar, 2) }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-amber-900 mb-1">Frecuencia</label>
                                    <select wire:model="frecuencia_pago" 
                                        class="mt-1 block w-full rounded-lg border-amber-300 bg-white font-semibold text-gray-900 shadow-sm focus:border-amber-500 h-10 sm:text-sm">
                                        <option value="diario">Diario</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="quincenal">Quincenal</option>
                                        <option value="mensual">Mensual</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-amber-900 mb-1">N° Cuotas</label>
                                    <input type="number" wire:model="numero_cuotas" placeholder="Ej: 12"
                                        class="mt-1 block w-full rounded-lg border-amber-300 bg-white font-bold text-gray-900 shadow-sm focus:border-amber-500 h-10 sm:text-sm">
                                    @error('numero_cuotas') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-3">
                        <flux:button type="submit" variant="primary" class="w-full justify-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl transition-all cursor-pointer shadow-md">
                            🛒 Procesar Nueva Venta
                        </flux:button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200">
                <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                    <span class="p-2 bg-gray-100 text-gray-700 rounded-lg">📋</span>
                    <h3 class="text-xl font-black text-gray-800">Historial Reciente de Ventas</h3>
                </div>
                
                @if($ventas->isEmpty())
                    <div class="text-center py-12 text-gray-400 font-medium text-sm">
                        No se han realizado ventas ni créditos en el sistema todavía.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Cliente</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Artículos Vendidos</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Tipo / Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Detalle Crédito</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                @foreach($ventas as $venta)
                                    <tr class="hover:bg-indigo-50/40 transition-colors" valign="top">
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 font-semibold">
                                            {{ date('d/m/Y', strtotime($venta->fecha_venta)) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900">{{ $venta->cliente->nombres }} {{ $venta->cliente->apellidos }}</div>
                                            <div class="text-gray-500 font-mono text-[10px]">DUI: {{ $venta->cliente_dui }}</div>
                                        </td>
                                        <td class="px-4 py-3 space-y-2">
                                            @if($venta->detalles && $venta->detalles->count() > 0)
                                                @foreach($venta->detalles as $detalle)
                                                    <div class="bg-indigo-50/50 border border-indigo-100 rounded p-1.5 max-w-[200px]">
                                                        <div class="flex justify-between items-center mb-0.5">
                                                            <span class="font-black text-indigo-700 text-[10px] bg-indigo-100 px-1 py-0.2 rounded">
                                                                {{ $detalle->producto->codigo_modelo ?? 'N/A' }}
                                                            </span>
                                                            <span class="text-gray-900 font-bold text-[10px]">x{{ $detalle->cantidad }}</span>
                                                        </div>
                                                        <div class="text-gray-700 font-medium text-[11px] truncate">{{ $detalle->producto->descripcion ?? 'Artículo Desconocido' }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                @if($venta->producto)
                                                    <div class="bg-gray-50 border border-gray-200 rounded p-1.5 max-w-[200px]">
                                                        <div class="flex justify-between items-center mb-0.5">
                                                            <span class="font-black text-gray-600 text-[10px] bg-gray-200 px-1 py-0.2 rounded">{{ $venta->producto->codigo_modelo }}</span>
                                                            <span class="text-gray-900 font-bold text-[10px]">x1</span>
                                                        </div>
                                                        <div class="text-gray-700 font-medium text-[11px] truncate">{{ $venta->producto->descripcion }}</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Sin artículos registrados</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded font-black uppercase text-[9px] block w-fit mb-1 shadow-sm border
                                                {{ $venta->tipo_pago === 'contado' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-purple-100 text-purple-800 border-purple-200' }}">
                                                {{ $venta->tipo_pago }}
                                            </span>
                                            <span class="font-black text-gray-900 text-sm">${{ number_format($venta->monto_total, 2) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            @if($venta->tipo_pago === 'credito')
                                                <p class="mb-0.5"><span class="font-bold text-gray-500">Prima:</span> ${{ number_format($venta->monto_prima, 2) }} | <span class="font-bold text-gray-500">Saldo:</span> <strong class="text-red-600">${{ number_format($venta->monto_financiar, 2) }}</strong></p>
                                                <p class="text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 w-fit text-[11px]">{{ $venta->numero_cuotas }} cuotas {{ $venta->frecuencia_pago }}s</p>
                                            @else
                                                <span class="text-gray-400 font-medium italic">N/A (Cancelado)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <flux:modal name="modal-cliente-rapido" class="md:w-[450px] space-y-6">
        <div class="flex items-center space-x-2 border-b pb-2">
            <span class="text-xl">👤</span>
            <div>
                <h3 class="text-lg font-black text-gray-800">Registro Rápido</h3>
                <p class="text-xs text-gray-500">Agrega al cliente sin abandonar el flujo de venta</p>
            </div>
        </div>

        <form wire:submit.prevent="guardarClienteRapido" x-on:submit="$flux.modal('modal-cliente-rapido').close()" class="space-y-4 text-left">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Número de DUI</label>
                <flux:input wire:model="nuevo_dui" placeholder="00000000-0" />
                @error('nuevo_dui') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Nombres</label>
                    <flux:input wire:model="nuevo_nombres" placeholder="Ej: Juan Antonio" />
                    @error('nuevo_nombres') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Apellidos</label>
                    <flux:input wire:model="nuevo_apellidos" placeholder="Ej: Pérez Gómez" />
                    @error('nuevo_apellidos') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Dirección Completa</label>
                <flux:input wire:model="nuevo_direccion" placeholder="Ej: Av. Roosevelt, Col. Las Flores, Casa #12" />
                @error('nuevo_direccion') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Teléfono</label>
                    <flux:input wire:model="nuevo_telefono" placeholder="Ej: 7777-7777" />
                    @error('nuevo_telefono') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Contacto / Referencia</label>
                    <flux:input wire:model="nuevo_contacto_referencia" placeholder="Ej: María (Hermana) - 2222-2222" />
                    @error('nuevo_contacto_referencia') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex space-x-2 pt-2 justify-end">
                <flux:button type="button" x-on:click="$flux.modal('modal-cliente-rapido').close()" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 rounded-lg cursor-pointer">
                    💾 Guardar y Seleccionar
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>