({
	mainConfigFile: "public/js/public/main.js",
    
	baseUrl: "./public/js/public",
    name: "main",
    
    paths: {
        requireLib: "../../vendor/require/require"
    },
    include: "requireLib",
    
    out: "./public/js/public/all.js"
})