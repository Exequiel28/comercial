<x-layouts::auth :title="__('Iniciar sesión')">
    <div class="fixed inset-0 flex items-center justify-center bg-cover bg-center antialiased font-sans" 
         style="background-image: url('https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1920');">
        
        <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-sm"></div>
       
        <div class="relative z-10 w-full max-w-md p-8 sm:p-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-800/50">
            <div>
         <img class="p-2" src="/images/logo-comercial-portillo.png">
       </div>
            
            <div class="flex flex-col gap-6">
                
                <x-auth-header :title="__('Inicia sesión en tu cuenta')" :description="__('Ingresa tu correo y contraseña a continuación para acceder')" />

                <x-auth-session-status class="text-center" :status="session('status')" />

                <x-passkey-verify />

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <flux:input
                        name="email"
                        :label="__('Correo electrónico')"
                        :value="old('email')"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="correo@ejemplo.com"
                    />

                    <div class="relative">
                        <flux:input
                            name="password"
                            :label="__('Contraseña')"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('Contraseña')"
                            viewable
                        />

                        @if (Route::has('password.request'))
                            <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </flux:link>
                        @endif
                    </div>

                    <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-2 rounded-xl border-none shadow-md transition-all active:scale-[0.99]" data-test="login-button">
                            {{ __('Ingresar') }}
                        </flux:button>
                    </div>
                </form>

                <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                    <span>{{ __('¿No tienes una cuenta?') }}</span>
                    <flux:link :href="route('register')" wire:navigate>{{ __('Regístrate') }}</flux:link>
                </div>
            </div>

        </div>
    </div>
</x-layouts::auth>