module.exports = {
	env: {
		browser: true,
		es2021: true
	},
	extends: [
		'standard',
		'plugin:vue/vue3-essential'
	],
	overrides: [
		{
			env: {
				node: true
			},
			files: [
				'.eslintrc.{js,cjs}'
			],
			parserOptions: {
				sourceType: 'script'
			}
		}
	],
	parserOptions: {
		ecmaVersion: 'latest',
		sourceType: 'module'
	},
	plugins: [
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
					"deprecated"
				], 
				"location": "anywhere" 
			}
		]
    }
}
