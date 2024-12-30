/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/register.php',
    './public/login.php',
    './public/home.php',
    './public/add_journal.php',
    './public/edit_journal.php',
  ],
  theme: {
    extend: {},
    colors: {
      customPink1: '#ffe9ec',
      customPink2: '#f5b6be',
      customPink3: '#de8bb0',
      customPurple1: '#cea9c6',
      customGray1: '#d9d9d9',
      customGray2: '#a6a6a6',
      customGray3: '#737373',
      red: '#ff3131',
      white: '#ffffff',
      black: '#000000',
    }
  },
  plugins: [],
}

