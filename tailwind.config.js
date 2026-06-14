const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    50: '#fdf8f0',
                    100: '#f9eddb',
                    200: '#f2d7b0',
                    300: '#e9bc7d',
                    400: '#dfa04e',
                    500: '#d4862f',
                    600: '#c06a22',
                    700: '#a0511e',
                    800: '#824220',
                    900: '#6b381e',
                    950: '#3a1b0e',
                },
                surface: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    800: '#1e1e2a',
                    900: '#14141f',
                    950: '#0a0a12',
                },
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
