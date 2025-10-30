/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js}"], // ou "./dist/**/*.{html,js}" se seu HTML estiver em /dist
  theme: {
  
    container: {
      center: true, // (centraliza) em todas as classes .container
      padding: {
        DEFAULT: '1rem',    // Padding padrão para telas pequenas (16px)
        sm: '2rem',       // Padding para telas 'sm' e acima (32px)
        lg: '4rem',       // Padding para telas 'lg' e acima (64px)
        xl: '5rem',       // Padding para telas 'xl' e acima (80px)
      },
    },
    // 👆 ADIÇÕES TERMINAM AQUI
    extend: {
      fontFamily: {
        Jost: ['"Jost"', 'sans-serif'],
        Lobster: ['"Lobster"', 'cursive'],
      },
    },
  },
  plugins: [],
}