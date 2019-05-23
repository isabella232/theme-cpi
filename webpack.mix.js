let mix = require('laravel-mix');

mix.options({
    processCssUrls: false,
})
    .disableNotifications()
    .js('static/js/app.js', 'dist/')
    .js('static/js/admin.js', 'dist/')
    .sass('static/scss/app.scss', 'dist/')
    .sass('static/scss/admin.scss', 'dist/')
    .sass('static/scss/docs.scss', 'dist/')

    .js('src/Blocks/Blocks.js', 'dist/blocks.build.js')
    .sass('src/Blocks/editor.scss', 'dist/block-editor.build.css')
    .options({
        autoprefixer: {
            options: {
                browsers: ['defaults', '> .5% in US', 'last 3 iOS versions', 'ie >= 10'],
            },
        },
    })
    .extract(['jquery', 'flickity', 'clipboard', 'instantsearch.js', 'underscore'])
    .autoload({
        jquery: ['$', 'window.jQuery'],
    })
    .browserSync({
        proxy: 'https://cpi.ups.dock',
        files: ['dist/**/*.+(css|js)', '*.php', 'templates/**/*.twig'],
    })
    .webpackConfig({
        output: {
            publicPath: '/wp-content/themes/cpi/',
            chunkFilename: 'dist/[name].[chunkhash].js',
        },
    });
