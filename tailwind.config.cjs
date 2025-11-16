module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#E53935',
        'primary-dark': '#C62828',
        surface: '#0f1113',
        muted: '#9CA3AF',
        'bg-dark': '#090909',
      },
      borderRadius: {
        xl: '12px',
        '2xl': '16px',
      },
      boxShadow: {
        'soft': '0 6px 18px rgba(0,0,0,0.6)',
      }
    },
  },
  plugins: [],
}
