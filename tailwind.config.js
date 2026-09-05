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
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#32745e', // Primary Brand Green
                    600: '#285e4c',
                    700: '#1e473a',
                    800: '#15332a',
                    900: '#0c1f19',
                    950: '#06110e',
                },
                accent: {
                    amber: '#f59e0b',
                    rose: '#f43f5e',
                    sky: '#0284c7',
                    indigo: '#6366f1',
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
