const { join, resolve } = require('path');
const path = require('path');

module.exports = () => { 
	return {
		resolve: { 
			alias: { 
				'@elysiumSlider/component': resolve( 
						join(__dirname, '..', 'src', 'component') 
				),
				'@elysiumSlider/module': resolve( 
						join(__dirname, '..', 'src', 'module', 'blur-elysium-slides') 
				),
				'@elysiumSlider/utilities/layout$': resolve( 
						join(__dirname, '..', 'src', 'utilities', 'layout.js') 
				),
				'@elysiumSlider/utilities/identifiers$': resolve( 
						join(__dirname, '..', 'src', 'utilities', 'identifiers.js') 
				),
				blurElysiumSlider: path.resolve(path.join(__dirname, '..', 'src'))
			} 
		} 
	}; 
}