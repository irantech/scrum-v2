const mix = require("laravel-mix");

// mix.js('resources/js/app.js', 'public/js')
//     .extract(['jquery'])
//     .sass('resources/sass/app.scss', 'public/css');

mix.setPublicPath('public/');
mix.js('resources/js/app.js', 'js')
    .extract(['jquery'])
    .sass('resources/sass/app.scss', 'css');
