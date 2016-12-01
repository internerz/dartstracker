const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix
        .copy('node_modules/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts/bootstrap')
        .sass('app.scss', 'public/css/app.css')
        .styles([
            './node_modules/jquery-ui-dist/jquery-ui.css',
            './public/css/app.css',
        ], 'public/css/style.css')
        .webpack('app.js', 'public/js/app.js')
        .scripts([
            './node_modules/lodash/lodash.js',
            './node_modules/jquery/dist/jquery.js',
            './node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
            './node_modules/jquery-ui-dist/jquery-ui.js',
            './public/js/app.js',
        ], 'public/js/scripts.js')
    ;
});
