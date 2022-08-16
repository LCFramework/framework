<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="ltr"
    class="antialiased bg-gray-100 dark"
>
    <head>
        <meta charset="UTF-8">
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <style>[x-cloak] {
                display: none !important;
            }</style>

        <link rel="stylesheet" href="{{mix('css/lcframework.css', 'lcframework')}}"/>
        <script src="{{mix('js/lcframework.js', 'lcframework')}}" defer></script>

        @livewireStyles
        @livewireScripts
        @stack('scripts')
    </head>
    <body class="bg-gray-100 text-gray-900 dark:text-gray-100 dark:bg-gray-900">
        @yield('content')

        @livewire('notifications')
    </body>
</html>
