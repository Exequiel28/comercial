<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800 font-medium shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    {{ $clienteId ? 'Modificar Datos de Cliente' : 'Registrar Nuevo Cliente' }}
                </h3>
                
                <form wire:submit.prevent="guardar" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">DUI</label>
                        <input type="text" wire:model="dui" placeholder="00000000-0" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('dui') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" wire:model="nombres" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('nombres') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" wire:model="apellidos" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('apellidos') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" wire:model="telefono" placeholder="7000-0000"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('telefono') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dirección Completa</label>
                        <textarea wire:model="direccion" rows="2" placeholder="Barrio, Calle, Municipio..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('direccion') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contacto de Referencia</label>
                        <textarea wire:model="contacto_referencia" rows="2" placeholder="Nombre, parentesco y teléfono del fiador o referencia..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('contacto_referencia') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <flux:button type="submit" variant="primary" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl transition-all cursor-pointer shadow-md">
                            {{ $clienteId ? '💾 Actualizar Cliente' : '💾 Registrar Nuevo Cliente' }}
                        </flux:button>

                        @if($clienteId)
                            <flux:button type="button" wire:click="cancelarEdicion" variant="ghost" class="rounded-xl border border-gray-300">
                                Cancelar
                            </flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8">
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Cartera de Clientes</h3>
                
                @if($clientes->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">
                        No hay clientes registrados en el sistema todavía.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">DUI</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dirección / Referencia</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @foreach($clientes as $cliente)
                                    <tr class="hover:bg-gray-50 transition-colors {{ method_exists($cliente, 'trashed') && $cliente->trashed() ? 'opacity-50 bg-gray-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $cliente->dui }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $cliente->nombres }} {{ $cliente->apellidos }}
                                            @if(method_exists($cliente, 'trashed') && $cliente->trashed())
                                                <span class="ml-2 px-1.5 py-0.5 text-[9px] bg-red-100 text-red-700 font-bold rounded uppercase">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $cliente->telefono }}</td>
                                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">
                                            <p class="truncate"><span class="font-medium text-xs">Dir:</span> {{ $cliente->direccion }}</p>
                                            <p class="truncate text-xs text-gray-400"><span class="font-medium">Ref:</span> {{ $cliente->contacto_referencia }}</p>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-xs flex items-center justify-center space-x-2">
                                            <button wire:click="editarCliente('{{ $cliente->dui }}')" title="Editar Cliente"
                                                class="inline-flex items-center p-1.5 bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white rounded-lg border border-amber-200 font-bold transition shadow-sm cursor-pointer">
                                                ✏️
                                            </button>

                                            <button wire:click="eliminarCliente('{{ $cliente->dui }}')" 
                                                wire:confirm="¿Estás seguro de que deseas deshabilitar a este cliente del sistema? Sus registros de ventas se mantendrán intactos."
                                                title="Eliminar Cliente"
                                                class="inline-flex items-center p-1.5 bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-200 font-bold transition shadow-sm cursor-pointer">
                                                🗑️
                                            </button>
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
</div>