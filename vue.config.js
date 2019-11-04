module.exports = {
    baseUrl: '/',
    outputDir: 'dist',
    publicPath: process.env.NODE_ENV === 'production' ? '/public' : '/',
    runtimeCompiler: true, 
    devServer: {
        port: "8080",
        proxy: {
            '/api': {
                target: 'http://live.miguvideo.com',  
                ws: true,  
                changeOrigin: true,  
                pathRewrite: {
                  '^/api': '' 
                },
                headers: {
                  referer: 'https://m.miguvideo.com/',
                  origin: 'm.miguvideo.com'
                }
            }
        }
    },
};