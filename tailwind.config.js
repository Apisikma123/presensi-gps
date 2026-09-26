import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    corePlugins: {
        preflight: false, // Prevents Tailwind base reset from conflicting with Bootstrap/Tabler vendor components
    },

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#FAF9F8', // Crema White
                    100: '#F4F3F2', // Surface Container Low
                    200: '#EEEEED', // Surface Container
                    300: '#DEC1B3', // Inverse Primary
                    400: '#AA9084', // On-Primary Container
                    500: '#3C2A21', // Primary Espresso Deep
                    600: '#25160E', // Dark Espresso
                    700: '#1A1C1C', // On-Surface
                    800: '#15110E',
                    900: '#0D0B09',
                    950: '#060504',
                },
                accent: {
                    matcha: '#4A6741', // Matcha Green
                    walnut: '#634832', // Roasted Walnut
                    amber: '#B45309',
                    rose: '#BA1A1A',
                    sky: '#0284C7',
                    indigo: '#634832',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', 'Menlo', 'monospace'],
            },
            boxShadow: {
                'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                'elevated': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
            },
            borderRadius: {
                'xl': '12px',
                '2xl': '16px',
                '3xl': '20px',
            },
        },
    },

    plugins: [
        forms({
            strategy: 'class', // Use .form-input, .form-select instead of global element overrides
        }),
    ],
};
