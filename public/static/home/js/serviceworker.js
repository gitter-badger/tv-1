'use strict';
(function () {
  'use strict';
    /**
    * Service Worker Toolbox caching
    */

    var cacheVersion = '-toolbox-v1';
    var dynamicVendorCacheName = 'dynamic-vendor' + cacheVersion;
    var staticVendorCacheName = 'static-vendor' + cacheVersion;
    var staticAssetsCacheName = 'static-assets' + cacheVersion;
    var contentCacheName = 'content' + cacheVersion;
    var maxEntries = 50;

    self.importScripts('sw-toolbox.js');

    self.toolbox.options.debug = false;

    // 缓存本站静态文件
    self.toolbox.router.get('/assets/(.*)', self.toolbox.cacheFirst, {
        cache: {
          name: staticAssetsCacheName,
          maxEntries: maxEntries
        }
    });

    // 缓存 googleapis
    self.toolbox.router.get('/css', self.toolbox.fastest, {
        origin: /fonts\.googleapis\.com/,
            cache: {
              name: dynamicVendorCacheName,
              maxEntries: maxEntries
            }
    });

    // 不缓存 DISQUS 评论
    self.toolbox.router.get('/(.*)', self.toolbox.networkOnly, {
        origin: /disqus\.com/
    });
    self.toolbox.router.get('/(.*)', self.toolbox.networkOnly, {
        origin: /disquscdn\.com/
    });


    // 缓存所有 Google 字体
    self.toolbox.router.get('/(.*)', self.toolbox.cacheFirst, {
        origin: /(fonts\.gstatic\.com|www\.google-analytics\.com)/,
        cache: {
          name: staticVendorCacheName,
          maxEntries: maxEntries
        }
    });

    self.toolbox.router.get('/content/(.*)', self.toolbox.fastest, {
        cache: {
          name: contentCacheName,
          maxEntries: maxEntries
        }
    });

    self.toolbox.router.get('/*', function (request, values, options) {
        if (!request.url.match(/(\/ghost\/|\/page\/)/) && request.headers.get('accept').includes('text/html')) {
            return self.toolbox.fastest(request, values, options);
        } else {
            return self.toolbox.networkOnly(request, values, options);
        }
        }, {
        cache: {
            name: contentCacheName,
            maxEntries: maxEntries
        }
    });

    // immediately activate this serviceworker
    self.addEventListener('install', function (event) {
        return event.waitUntil(self.skipWaiting());
    });

    self.addEventListener('activate', function (event) {
        return event.waitUntil(self.clients.claim());
    }); 

})();