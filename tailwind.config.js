const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js'
    ],
    theme: {
        extend: {
            colors: {
                gray: defaultTheme.colors.neutral
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms')
    ]
};
