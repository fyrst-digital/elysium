const { join, resolve } = require('path');

module.exports = () => { 
    return {
        module: {
            rules: [
                {
                    test: /\.css$/i,
                    use: ["style-loader", "css-loader"]
                }
            ]
        },
        resolve: { 
           alias: { 
               '@splide': resolve( 
                    join(__dirname, '..', 'node_modules', '@splidejs', 'splide') 
               ) 
           } 
       } 
   }; 
}