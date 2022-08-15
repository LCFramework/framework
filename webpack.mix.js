const mix = require('laravel-mix');

mix.disableSuccessNotifications();
mix.options({
    terser: {
        extractComments: false
    }
});
mix.setPublicPath('dist');
mix.sourceMaps();
mix.version();

mix.js('resources/js/lcframework.js', 'dist/js');

mix.postCss('resources/css/lcframework.css', 'dist/css', [
    require('tailwindcss')
]).postCss('resources/css/filament.css', 'dist/css', [
    require('tailwindcss')
]);
