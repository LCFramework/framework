const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js'
    ],
    theme: {
        extend: {
            colors: {
                gray: colors.neutral
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms')
    ]
};
