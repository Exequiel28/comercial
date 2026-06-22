<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session()->has('message'))
            <div class="mb-6 p-4 text-sm text-emerald-900 bg-emerald-200 border-l-4 border-emerald-600 rounded-r-lg font-bold shadow-md">
                ✅ {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="space-y-6">
                
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                    <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                        <span class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">🔍</span>
                        <h3 class="text-base font-black text-gray-800 uppercase tracking-wider">Buscar Cliente</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Ingrese el número de DUI exacto:</label>
                        <flux:input wire:model.live.debounce.500ms="searchDui" placeholder="Ej: 00000000-0" icon="identification" class="font-bold text-gray-900" />
                    </div>

                    @if(!empty($searchDui))
                        <div class="mt-3 text-xs">
                            @if($clienteEncontrado)
                                <span class="text-emerald-600 font-bold flex items-center gap-1">🟢 Cliente localizado: {{ $clienteEncontrado->nombres }} {{ $clienteEncontrado->apellidos }}</span>
                            @else
                                <span class="text-rose-500 font-medium flex items-center gap-1">❌ No hay registros con este número de DUI.</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                    <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                        <span class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">💵</span>
                        <h3 class="text-lg font-black text-gray-800">Recibo de Abono</h3>
                    </div>
                    
                    <form wire:submit.prevent="guardar" class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Seleccione la cuenta a abonar:</label>
                            <select wire:model.live="venta_id" {{ $creditos_activos->isEmpty() ? 'disabled' : '' }}
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-bold shadow-sm focus:border-emerald-600 focus:ring focus:ring-emerald-200 h-10 sm:text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                                @if($creditos_activos->isEmpty())
                                    <option value="">-- Sin cuentas pendientes --</option>
                                @else
                                    <option value="">-- Elija uno de sus {{ $creditos_activos->count() }} créditos activos --</option>
                                    @foreach($creditos_activos as $credito)
                                        <option value="{{ $credito->id }}">
                                            Ref: Venta #{{ $credito->id }} | 
                                            @if($credito->detalles && $credito->detalles->count() > 0)
                                                {{ $credito->detalles->map(fn($d) => ($d->producto->descripcion ?? 'Artículo') . " (x" . $d->cantidad . ")")->implode(', ') }}
                                            @else
                                                {{ $credito->producto->descripcion ?? 'Artículo comercial' }}
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('venta_id') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>

                        @if($detalles_credito)
                            <div class="bg-teal-100 p-4 sm:p-5 rounded-xl text-gray-800 shadow-md border border-teal-200 space-y-3" wire:key="resumen-{{ $venta_id }}">
                                
                                <div class="border-b border-teal-200 pb-2 flex justify-between items-center gap-2">
                                    <p class="text-[11px] sm:text-xs font-black text-teal-950 uppercase tracking-widest flex items-center gap-1">📊 Resumen del Crédito</p>
                                    
                                    <div>
                                        <flux:button type="button" variant="primary" color="emerald" wire:click="descargarHistorial" class="bg-emerald-700 hover:bg-emerald-800 font-bold px-3 py-1.5 rounded-lg text-[10px] sm:text-xs transition-colors duration-200 flex items-center gap-1 cursor-pointer shadow-md inline-block text-white">
                                            📋 Descargar Historial
                                        </flux:button>
                                    </div>
                                </div>

                                <div class="space-y-1 text-xs">
                                    <p class="text-teal-950">
                                        <span class="text-teal-800 font-bold text-[10px] uppercase block tracking-wider">Titular de la Cuenta:</span> 
                                        <strong class="text-xs sm:text-sm font-black text-teal-950 break-words">{{ $detalles_credito['cliente'] }}</strong>
                                    </p>
                                    
                                    <p class="text-teal-950 pt-1">
                                        <span class="text-teal-800 font-bold text-[10px] uppercase block tracking-wider mb-0.5">Estructura de Pagos:</span> 
                                        <span class="inline-block bg-teal-200 border border-teal-300 text-teal-900 px-2 py-0.5 rounded text-[10px] font-black">
                                            {{ $detalles_credito['cuotas_pactadas'] }}
                                        </span>
                                    </p>
                                </div>

                                <div class="space-y-1.5 pt-1 text-xs border-t border-teal-200/60">
                                    <div class="flex justify-between items-center bg-white/50 p-1.5 rounded px-2.5">
                                        <span class="text-teal-900 font-bold">💰 Total de la Venta:</span>
                                        <span class="font-black text-teal-950">${{ number_format($detalles_credito['monto_total'] ?? 0, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between items-center bg-white/50 p-1.5 rounded px-2.5">
                                        <span class="text-emerald-800 font-bold">🔴 Prima Entrega Inicial:</span>
                                        <span class="font-black text-emerald-800">-${{ number_format($detalles_credito['monto_prima'] ?? 0, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between items-center bg-teal-50 p-1.5 rounded px-2.5 border border-teal-200/50">
                                        <span class="text-teal-900 font-medium">📅 Inicial Financiado:</span>
                                        <span class="font-black text-teal-950">${{ number_format($detalles_credito['monto_financiar'], 2) }}</span>
                                    </div>

                                    <div class="flex justify-between items-center bg-white/50 p-1.5 rounded px-2.5">
                                        <span class="text-teal-900 font-medium">✨ Total Abonado (Cuotas):</span>
                                        <span class="font-bold text-emerald-800">${{ number_format($detalles_credito['total_abonado'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-amber-50 p-3 sm:p-4 rounded-xl border-2 border-amber-300 shadow-sm" wire:key="cuota-{{ $venta_id }}">
                                <div class="flex justify-between items-center gap-2">
                                    <div>
                                        <p class="text-[11px] sm:text-xs font-black text-amber-900 uppercase tracking-wide">Cuota Sugerida</p>
                                        <p class="text-[9px] sm:text-[10px] text-amber-700 font-medium">Matemática del contrato</p>
                                    </div>
                                    <span class="text-base sm:text-xl font-black text-amber-950 bg-white border border-amber-300 px-2.5 py-1 rounded-lg shadow-sm whitespace-nowrap">
                                        ${{ number_format($detalles_credito['cuota_calculada'], 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-rose-50 p-3 sm:p-4 rounded-xl text-center border-2 border-rose-300 shadow-sm" wire:key="saldo-{{ $venta_id }}">
                                <p class="text-[11px] sm:text-xs uppercase tracking-wider text-rose-900 font-black">Saldo Pendiente Actual</p>
                                <p class="text-2xl sm:text-3xl font-black text-red-600 drop-shadow-sm">${{ number_format($detalles_credito['saldo_pendiente'], 2) }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Fecha de Cobro</label>
                                <input type="date" wire:model="fecha_abono" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 font-semibold shadow-sm focus:border-emerald-600 h-10 sm:text-sm">
                                @error('fecha_abono') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-emerald-800 mb-1">Monto Efectivo ($)</label>
                                <input type="number" step="0.01" wire:model="monto_abono" placeholder="0.00"
                                    class="mt-1 block w-full rounded-lg border-2 border-emerald-400 bg-emerald-50 text-base font-black text-emerald-900 shadow-sm focus:border-emerald-600 focus:ring focus:ring-emerald-200 h-10 text-lg">
                                @error('monto_abono') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <p class="text-[10px] text-gray-500 font-medium italic bg-gray-100 p-2 rounded-lg border">💡 Tip Comercial: El cliente puede abonar más del valor sugerido para liquidar rápido su cuenta.</p>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Concepto / Comentario</label>
                            <input type="text" wire:model="nota" placeholder="Ej: Abono cuota 3 / Pago extra"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:border-emerald-600 h-10 sm:text-sm">
                            @error('nota') <span class="text-xs text-red-600 mt-1 block font-bold">⚠️ {{ $message }}</span> @enderror
                        </div>

                        <div class="pt-2">
                            <flux:button type="submit" variant="primary" class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition-all cursor-pointer shadow-md">
                                💵 Aplicar y Registrar Abono
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200 h-fit">
                <div class="flex items-center space-x-2 mb-4 border-b pb-3">
                    <span class="p-2 bg-gray-100 text-gray-700 rounded-lg">🗂️</span>
                    <h3 class="text-xl font-black text-gray-800">Últimos Recibos Emitidos (Arqueo de Caja)</h3>
                </div>
                
                @if($ultimos_abonos->isEmpty())
                    <div class="text-center py-12 text-gray-400 font-medium text-sm">
                        No se han registrado abonos en caja todavía.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Recibo N°</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Cliente / Deudor</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Detalle Comercial</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Monto Abonado</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="max-h-[550px] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                    @foreach($ultimos_abonos as $abono)
                                        <tr class="hover:bg-emerald-50/30 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap font-mono text-indigo-700 font-black bg-indigo-50/50 text-center border-r">
                                                #{{ str_pad($abono->id, 6, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 font-semibold">
                                                {{ date('d/m/Y', strtotime($abono->fecha_abono)) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-900">
                                                    {{ $abono->venta->cliente->nombres ?? 'N/A' }} {{ $abono->venta->cliente->apellidos ?? '' }}
                                                </div>
                                                <div class="text-gray-500 font-mono text-[10px]">DUI: {{ $abono->venta->cliente->dui ?? $abono->venta->cliente_dui ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="text-indigo-700 font-bold">Ref: Venta #{{ $abono->venta_id }}</div>
                                                
                                                <div class="text-[11px] font-semibold text-gray-800 space-y-0.5 my-1">
                                                    @if($abono->venta && $abono->venta->detalles)
                                                        @foreach($abono->venta->detalles as $det)
                                                            <div class="truncate max-w-[220px] text-gray-600">📦 {{ $det->producto->descripcion ?? 'Artículo' }} (x{{ $det->cantidad }})</div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="text-[10px] font-medium text-gray-400 italic truncate max-w-[180px]">Obs: {{ $abono->nota ?? 'Sin observaciones' }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap font-black text-emerald-700 text-sm bg-emerald-50/30">
                                                💰 +${{ number_format($abono->monto_abono, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>