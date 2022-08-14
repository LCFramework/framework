const defaultConfig = require('tailwindcss/defualtConfig');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js'
    ],
    theme: {
        extend: {
            colors: {
                gray: defaultConfig.colors.neutral
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms')
    ]
};
