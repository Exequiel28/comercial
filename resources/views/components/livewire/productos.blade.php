<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session()->has('message'))
            <div class="mb-6 p-4 text-sm text-blue-900 bg-blue-200 border-l-4 border-blue-600 rounded-r-lg font-bold shadow-md">
                📦 {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 h-fit">
                <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                    <span class="p-2 bg-blue-100 text-blue-700 rounded-lg">📥</span>
                    <h3 class="text-xl font-black text-gray-800">Registrar Artículo</h3>
                </div>
                
                <form wire:submit.prevent="guardar" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Código o Modelo</label>
                        <input type="text" wire:model="codigo_modelo" placeholder="Ej: TV-LG-55SMART" 
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-mono font-bold shadow-sm focus:border-blue-600 h-10 sm:text-sm uppercase">
                        @error('codigo_modelo') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Marca</label>
                        <input type="text" wire:model="marca" placeholder="Ej: Mabe, Sony, Olimpia" 
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-medium shadow-sm focus:border-blue-600 h-10 sm:text-sm">
                        @error('marca') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Descripción de Exhibición</label>
                        <textarea wire:model="descripcion" rows="2" placeholder="Ej: Refrigeradora inverter gris de 14 pies..."
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-medium shadow-sm focus:border-blue-600 sm:text-sm"></textarea>
                        @error('descripcion') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Precio ($)</label>
                            <input type="number" step="0.01" wire:model="precio" placeholder="0.00"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-black text-gray-900 shadow-sm focus:border-blue-600 h-10 sm:text-sm">
                            @error('precio') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Stock Inicial</label>
                            <input type="number" wire:model="stock" placeholder="Ej: 5"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-bold text-gray-900 shadow-sm focus:border-blue-600 h-10 sm:text-sm">
                            @error('stock') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <flux:button type="submit" 
                            variant="primary" class="w-full inline-flex justify-center items-center rounded-lg bg-blue-700 py-3 px-4 text-base font-black text-white shadow-lg hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all transform active:scale-95 cursor-pointer">
                            📦 INGRESAR A BODEGA
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8">
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                    <span class="p-2 bg-gray-100 text-gray-700 rounded-lg">📊</span>
                    <h3 class="text-xl font-black text-gray-800">Control de Existencias / Almacén</h3>
                </div>
                
                @if($productos->isEmpty())
                    <div class="text-center py-12 text-gray-400 font-medium text-sm">
                        No hay artículos registrados en bodega todavía.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Código/Modelo</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Descripción</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Marca</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Precio</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Existencias</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Acciones / Estado</th> </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @foreach($productos as $producto)
                                    <tr class="hover:bg-blue-50/30 transition-colors {{ $producto->trashed() ? 'opacity-55 bg-gray-50/50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-mono text-xs font-black text-blue-800 bg-blue-50/50 rounded-md border border-blue-100">
                                            {{ $producto->codigo_modelo }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800 font-bold text-xs">
                                            {{ $producto->descripcion }}
                                            @if($producto->trashed())
                                                <span class="ml-2 px-1.5 py-0.5 text-[9px] bg-red-100 text-red-700 font-black rounded uppercase tracking-wide">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 font-semibold text-xs">{{ $producto->marca }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap font-black text-gray-900">${{ number_format($producto->precio, 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-xs font-black shadow-sm border block w-fit
                                                {{ $producto->stock > 2 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200 animate-pulse' }}">
                                                📦 {{ $producto->stock }} unidades
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-xs flex items-center justify-center space-x-2">
                                            <button wire:click="abrirModalStock({{ $producto->id }})" title="Añadir Inventario"
                                                    class="inline-flex items-center p-1.5 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white rounded-lg border border-green-200 font-bold transition shadow-sm cursor-pointer">
                                                ➕
                                            </button>

                                            <button wire:click="editarProducto({{ $producto->id }})" title="Editar Producto"
                                                class="inline-flex items-center p-1.5 bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white rounded-lg border border-amber-200 font-bold transition shadow-sm cursor-pointer">
                                                ✏️
                                            </button>

                                            <div class="inline-flex items-center justify-center pt-0.5" title="{{ $producto->trashed() ? 'Habilitar Producto' : 'Deshabilitar Producto' }}">
                                                <flux:switch 
                                                    wire:click="toggleEstado({{ $producto->id }})" 
                                                    :checked="!$producto->trashed()" 
                                                />
                                            </div>
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
    
    <flux:modal name="modal-add-stock" class="md:w-[400px] space-y-6">
        <div class="flex items-center space-x-2 border-b pb-2">
            <span class="text-xl">📦</span>
            <div>
                <h3 class="text-lg font-black text-gray-800">Abastecer Almacén</h3>
                <p class="text-xs text-gray-500 truncate max-w-[280px]">{{ $nombre_producto_stock }}</p>
            </div>
        </div>

        <form wire:submit.prevent="guardarStockRapido" class="space-y-4 text-left">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">Cantidad de unidades a ingresar</label>
                <flux:input type="number" wire:model="cantidad_a_sumar" placeholder="Ej: 10, 25, 50" min="1" />
                @error('cantidad_a_sumar') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
            </div>

            <div class="flex space-x-2 pt-2 justify-end">
                <flux:button type="button" x-on:click="$flux.modal('modal-add-stock').close()" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" class="bg-green-600 hover:bg-green-700 text-white font-bold px-4 rounded-lg cursor-pointer">
                    ➕ Sumar al Stock
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>