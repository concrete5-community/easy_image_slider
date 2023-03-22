const mix = require('laravel-mix');

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
mix.setPublicPath('../blocks/easy_image_slider');

mix
    .js('src/block-edit.js', 'js/block-edit.js')
    .js('src/imagesloaded.pkgd.js', 'js/imagesloaded.js')
    .js('src/intense.js', 'js/intense.js')
    .js('src/jquery.knob.js', 'js/jquery.knob.js')
    .js('src/jquery.prettyPhoto.js', 'js/jquery.prettyPhoto.js')
    .js('src/owl.carousel.js', 'js/owl.carousel.js')
;
