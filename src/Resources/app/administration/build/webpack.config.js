const path = require('path');

module.exports = (webpack) => {
    // Exclude the plugin's icons from being loaded via a url-loader
    webpack.config.module.rules.forEach((rule) => {
        if (rule.loader === 'url-loader') {
            if (!rule.exclude) {
                rule.exclude = [];
            }
            rule.exclude.push(
                path.join(__dirname, '..', 'src', 'icons', 'blurph')
            );
        }
    });

    return {
        resolve: {
            alias: {
                blurElysium: path.join(__dirname, '..', 'src')
            }
        },
        module: {
            rules: [
                {
                    test: /\.svg/,
                    type: 'asset/source'
                }
            ]
        }
    };
};