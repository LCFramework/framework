const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/filament/**/*.blade.php'
    ],
    theme: {
        extend: {
            colors: {
                gray: colors.neutral,
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.yellow
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ]
};
