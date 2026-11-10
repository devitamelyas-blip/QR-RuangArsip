import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0f5fa',
                    100: '#e1ecf5',
                    200: '#c3d9eb',
                    300: '#94bcdd',
                    400: '#5e9bc9',
                    500: '#387eb3',
                    600: '#276497',
                    700: '#1f507b',
                    800: '#1b4366',
                    900: '#0F2642',
                    950: '#0A1B2F',
                },
                pelita: {
                    navy: '#0A2540',
                    'navy-dark': '#061729',
                    'navy-light': '#14385C',
                    red: '#C8102E',
                    'red-dark': '#A00D24',
                    'red-light': '#E52B44',
                    gold: '#C5A059',
                    surface: '#FFFFFF',
                    bg: '#F5F7FA',
                    border: '#E2E8F0',
                },
            },
        },
    },

    plugins: [forms],
};
