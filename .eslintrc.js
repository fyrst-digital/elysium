module.exports = {
	env: {
		browser: true,
		es2021: true
	},
	extends: [
		'eslint:recommended',
		'plugin:@typescript-eslint/recommended',
		'plugin:vue/vue3-essential'
	],

	parserOptions: {
		parser: "@typescript-eslint/parser",
		ecmaVersion: 'latest',
		sourceType: 'module'
	},
	plugins: [
		'@typescript-eslint',
		'vue'
	],
	rules: {
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
