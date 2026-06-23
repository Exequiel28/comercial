<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Comercial Portillo') : config('app.name', 'Comercial Portillo') }}
</title>

<link rel="icon" href="/logop.png" sizes="any">
<link rel="icon" href="/logop.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/logop.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
