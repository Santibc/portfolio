const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       // Si no usas Tailwind, puedes quitar esta línea
  
   ])
   .sourceMaps();