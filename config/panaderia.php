<?php

/*
|--------------------------------------------------------------------------
| Landing pública
|--------------------------------------------------------------------------
|
| El contenido (datos del negocio, textos, fotos, productos…) se edita desde
| el módulo "Página web" del panel y vive en la base de datos (tablas landing_*).
| Aquí solo queda lo que depende del entorno.
|
*/

return [

    // Sin key el mapa usa el embed público de Google; con key usa la Maps Embed API oficial.
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),

];
