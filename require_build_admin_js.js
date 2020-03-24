({
	mainConfigFile: "public/js/admin/main.js",
    
	baseUrl: "./public/js/admin",
    name: "main",
    
    paths: {
        requireLib: "../../vendor/require/require"
    },
    include: "requireLib",
    exclude: ["ckeditor.core"],
    
    out: "./public/js/admin/all.js"
})