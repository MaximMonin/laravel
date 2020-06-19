const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('node_modules/popper.js/dist/popper.js.map', 'public/js/popper.js.map')   
    .copy('node_modules/dropzone/dist/dropzone.js', 'public/js/dropzone.js')
    .copy('node_modules/dropzone/dist/dropzone.css', 'public/css/dropzone.css')
    .extract(['jquery', 'bootstrap', 'lodash', 'popper.js', 'vue', 'axios', 'vuetify', 'vue2-editor']);
