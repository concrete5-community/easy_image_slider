const path = require('path');
const mix = require('laravel-mix');

const PACKAGE_DIR = path.dirname(__dirname);

mix
    .disableNotifications()
    .webpackConfig({
        cache: false,
        resolve: {
            symlinks: false
        },
        externals: {
            jquery: 'jQuery'
        },
    })
    .options({
        clearConsole: false,
        // Disable extracting licenses from comments
        terser: {
            extractComments: false,
        },
    })
;
mix.setPublicPath(PACKAGE_DIR);

mix
    .js('src/block-edit.js', 'js/block-edit.js')
    .css('src/block-edit.css', 'css/block-edit.css')
    .css('src/block-view.css', 'css/block-view.css')
    .js('src/imagesloaded/imagesloaded.pkgd.js', 'js/imagesloaded.js')
    .js('src/intense.js', 'js/intense.js')
    .js('src/jquery.knob.js', 'js/jquery.knob.js')
    .js('src/jquery.prettyPhoto/jquery.prettyPhoto.js', 'js/jquery.prettyPhoto.js')
    .css('src/jquery.prettyPhoto/prettyPhoto.css', 'css/prettyPhoto.css', {processUrls: false})
    .copy('src/images/prettyPhoto/', PACKAGE_DIR + '/images/prettyPhoto')
    .js('src/owl.carousel/owl.carousel.js', 'js/owl.carousel.js')
    .css('src/owl.carousel/owl.carousel.css', 'css/owl.carousel.css', {processUrls: false})
    .copy('src/owl.carousel/grabbing.png', PACKAGE_DIR + '/css/grabbing.png')
    .css('src/owl.carousel/owl.theme.css', 'css/owl.theme.css', {processUrls: false})
    .copy('src/owl.carousel/AjaxLoader.gif', PACKAGE_DIR + '/css/AjaxLoader.gif')
    .css('src/owl.carousel/owl.transitions.css', 'css/owl.transitions.css')
    .css('src/animate/animate.css', 'css/animate.css')
    .js('src/x-editable/js/bootstrap-editable.js', 'js/bootstrap-editable.js')
    .css('src/x-editable/css/bootstrap-editable.css', 'css/bootstrap-editable.css', {processUrls: false})
    .copy('src/x-editable/img/', PACKAGE_DIR + '/img')
;
