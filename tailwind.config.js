/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ["./pages/**/*.{html,php}", "./php/**/*.php", "./components/**/*.{html,php}", "./view/**/*.{html,php}"],
  safelist: [
    "bg-orange-500",
    "bg-yellow-500"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

