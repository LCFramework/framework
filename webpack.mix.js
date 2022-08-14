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

mix.postCss('resources/css/lcframework.css', 'dist/css', [
    require('tailwindcss')
]);
