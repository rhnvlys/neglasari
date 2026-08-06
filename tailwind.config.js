/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'neglasari-main': '#1F5D42',
        'neglasari-dark': '#173E2E',
        'neglasari-accent': '#2F7D5A',
        'neglasari-bg': '#F6F8F6',
        'neglasari-text': '#17201B',
        'neglasari-text-secondary': '#66736B',
        'neglasari-border': '#DDE5DF',
      }
    },
  },
  plugins: [],
}
