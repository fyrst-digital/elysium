module.exports = {
	env: {
		browser: true,
		es2023: true
	},
	extends: [
		'eslint:recommended',
		'plugin:@typescript-eslint/recommended',
		/**
		 * @todo Include strict typescript rules to improve typing.
		 * Uncomment next line to have fun 
		 */		
		// 'plugin:@typescript-eslint/recommended-requiring-type-checking',
		'plugin:vue/vue3-essential'
	],
	parser: "@typescript-eslint/parser",
	parserOptions: {
		
		project: "./src/Resources/app/administration/tsconfig.json",
		// ecmaVersion: 'latest',
		sourceType: 'module'
	},
	plugins: [
		'@typescript-eslint',
		'vue'
	],
	rules: {
		/**
		 * @todo Include strict typescript rules to improve typing
		 */
		// "@typescript-eslint/strict-boolean-expressions": "error", 
		"@typescript-eslint/no-explicit-any": "error",       
		"@typescript-eslint/no-unused-vars": "error",     
		"@typescript-eslint/no-non-null-assertion": "error",      
        "indent": [0, "tab"],
        "no-tabs": 0,
		"no-console": [
			"warn", {
				allow: ["warn", "error"]
			}
		],
		"no-warning-comments": [
			"warn", 
			{ 
				"terms": [ 
					"deprecated", "todo"
				], 
				"location": "anywhere" 
			}
		]
    }
}
