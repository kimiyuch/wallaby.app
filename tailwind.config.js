import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Lilac: the icon's background. The brand colour: buttons, links, focus.
                lilac: {
                    50: '#f6f3fe',
                    100: '#eee8fd',
                    200: '#d9cbfa',
                    300: '#c6b3f7',
                    400: '#a68cec',
                    500: '#8b6be0',
                    600: '#7556cf',
                    700: '#6243b3',
                    800: '#523a92',
                    900: '#453377',
                    950: '#2a1d4d',
                },
                // Mint: the wallet. Money in, balances that are even, settled months.
                mint: {
                    50: '#edfbf6',
                    100: '#d3f5e8',
                    200: '#aaead5',
                    300: '#73d9bc',
                    400: '#4fc7a2',
                    500: '#2fae8a',
                    600: '#22977a',
                    700: '#1b7d65',
                    800: '#1a6352',
                    900: '#185245',
                    950: '#082f28',
                },
                // Gold: the coin. Highlights and amounts owed.
                gold: {
                    50: '#fffbeb',
                    100: '#fff3c6',
                    200: '#ffe08a',
                    300: '#ffd05c',
                    400: '#fbbd3b',
                    500: '#f0a92e',
                    600: '#d98f1c',
                    700: '#b46a16',
                    800: '#925318',
                    900: '#784518',
                    950: '#452308',
                },
            },
        },
    },

    plugins: [forms],
};
