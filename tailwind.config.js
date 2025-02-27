import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                green: {
                    100: '#dcfce7',
                    600: '#16a34a',
                },
                yellow: {
                    100: '#fef9c3',
                    600: '#ca8a04',
                },
                gray: {
                    100: '#f3f4f6',
                    600: '#4b5563',
                }
            },
        },
    },
    plugins: [],
};
