const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
    outputDir: "resources/lib",
    configureWebpack: {
        optimization: {
            minimizer : [
                new UglifyJsPlugin({
                    uglifyOptions: {
                        mangle: true
                    }
                })
            ]
        }
    },
    css: {
        extract: false
    }
}
