<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        
        <!-- CABECERA DE BIENVENIDA -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/60 pb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Comercial Exequiel</p>
                <h3 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white mt-1">
                    ¡Bienvenido, {{ auth()->user()->name }}! 👋
                </h3>
            </div>
            
            <div class="inline-flex items-center gap-2 self-start md:self-center px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">Sistema Activo</span>
            </div>
        </div>

        <!-- SECCIÓN DE MÉTRICAS (GRILLA REAL DE 4 COLUMNAS) -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Ventas -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-sm">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-xs font-bold uppercase tracking-wider">Ventas del Mes</span>
                    <span class="text-lg">📈</span>
                </div>
                <div class="mt-2">
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">$4,850.00</h3>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">↑ 12.4% <span class="text-zinc-400 font-normal">vs mes anterior</span></p>
                </div>
            </div>

            <!-- Cartera -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-sm">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-xs font-bold uppercase tracking-wider">Cartera de Créditos</span>
                    <span class="text-lg">💳</span>
                </div>
                <div class="mt-2">
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">$12,340.00</h3>
                    <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">• 18 Clientes activos</p>
                </div>
            </div>

            <!-- Caja -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-sm">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-xs font-bold uppercase tracking-wider">Recaudado Hoy</span>
                    <span class="text-lg">💵</span>
                </div>
                <div class="mt-2">
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">$340.00</h3>
                    <p class="text-[11px] text-zinc-400 font-medium mt-0.5">Efectivo por abonos</p>
                </div>
            </div>

            <!-- Stock -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-sm">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-xs font-bold uppercase tracking-wider">Alertas Stock</span>
                    <span class="text-lg">🚨</span>
                </div>
                <div class="mt-2">
                    <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400">3 Ítems</h3>
                    <p class="text-[11px] text-rose-500 font-semibold mt-0.5">⚠️ Requieren revisión</p>
                </div>
            </div>

        </div>

        <!-- SECCIÓN DE ACCESOS DIRECTOS (GRILLA DE 4 COLUMNAS) -->
        <div class="flex flex-col gap-3 mt-2">
            <h4 class="text-xs font-bold uppercase tracking-widest text-zinc-400">Accesos Rápidos a Módulos</h4>
            
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                
                <!-- Tarjeta: CLIENTES -->
                <a href="{{ route('clientes') }}" class="group relative block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-indigo-500">
                    <div class="flex items-center justify-between mb-4">
                        <span class="rounded-lg bg-indigo-50 p-2.5 text-xl text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            👥
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400">Clientes</span>
                    </div>
                    <h4 class="text-base font-black text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Gestión Clientes</h4>
                    <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 leading-normal">Perfiles, DUI y direcciones de entrega.</p>
                </a>

                <!-- Tarjeta: INVENTARIO -->
                <a href="{{ route('productos') }}" class="group relative block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-amber-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-500">
                    <div class="flex items-center justify-between mb-4">
                        <span class="rounded-lg bg-amber-50 p-2.5 text-xl text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            📦
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-50 px-2 py-0.5 rounded text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">Bodega</span>
                    </div>
                    <h4 class="text-base font-black text-zinc-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Inventario</h4>
                    <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 leading-normal">Control de stock, marcas y precios de lista.</p>
                </a>

                <!-- Tarjeta: VENTAS -->
                <a href="{{ route('ventas') }}" class="group relative block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-purple-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-purple-500">
                    <div class="flex items-center justify-between mb-4">
                        <span class="rounded-lg bg-purple-50 p-2.5 text-xl text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            🛒
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-purple-50 px-2 py-0.5 rounded text-purple-600 dark:bg-purple-950/40 dark:text-purple-400">Vender</span>
                    </div>
                    <h4 class="text-base font-black text-zinc-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Ventas y Créditos</h4>
                    <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 leading-normal">Facturación al contado, cuotas y primas.</p>
                </a>

                <!-- Tarjeta: ABONOS -->
                <a href="{{ route('abonos') }}" class="group relative block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-emerald-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-500">
                    <div class="flex items-center justify-between mb-4">
                        <span class="rounded-lg bg-emerald-50 p-2.5 text-xl text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            💵
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">Cobros</span>
                    </div>
                    <h4 class="text-base font-black text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Caja y Abonos</h4>
                    <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 leading-normal">Recaudación, cobro de cuotas y recibos.</p>
                </a>

            </div>
        </div>

    </div>
</x-layouts::app>