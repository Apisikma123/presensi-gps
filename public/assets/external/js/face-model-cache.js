/**
 * Face Recognition Model & Descriptors Caching Utility (High Performance Engine)
 * - Intercepts model fetches (/models/*) to serve binary weights directly from IndexedDB.
 * - Caches neural network weights (7MB) once in IndexedDB; subsequent page loads resolve in < 10ms with zero network requests.
 * - Caches employee face descriptors in IndexedDB for instant matcher recreation without re-running CNN on photos.
 * - Avoids re-parsing models if faceapi nets are already loaded in memory.
 * - Background preloading support for dashboard.
 */
(function () {
    'use strict';

    const DB_NAME = 'FaceRecognitionCache';
    const DB_VERSION = 3;
    const STORE_MODELS = 'model_files';
    const STORE_DESCRIPTORS = 'descriptors';
    const CACHE_EXPIRY = 7 * 24 * 60 * 60 * 1000; // 7 days

    let dbPromise = null;

    function getDB() {
        if (!dbPromise) {
            dbPromise = new Promise((resolve, reject) => {
                const request = indexedDB.open(DB_NAME, DB_VERSION);
                request.onerror = () => reject(request.error);
                request.onsuccess = () => resolve(request.result);
                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_MODELS)) {
                        db.createObjectStore(STORE_MODELS);
                    }
                    if (!db.objectStoreNames.contains(STORE_DESCRIPTORS)) {
                        db.createObjectStore(STORE_DESCRIPTORS);
                    }
                };
            });
        }
        return dbPromise;
    }

    // Get model file (ArrayBuffer or JSON) from IndexedDB
    async function getCachedModelFile(key) {
        try {
            const db = await getDB();
            return new Promise((resolve) => {
                const tx = db.transaction([STORE_MODELS], 'readonly');
                const req = tx.objectStore(STORE_MODELS).get(key);
                req.onsuccess = () => {
                    const item = req.result;
                    if (item && (Date.now() - item.timestamp) < CACHE_EXPIRY) {
                        resolve(item);
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = () => resolve(null);
            });
        } catch (e) {
            return null;
        }
    }

    // Save model file to IndexedDB
    async function saveModelFile(key, buffer, contentType) {
        try {
            const db = await getDB();
            const tx = db.transaction([STORE_MODELS], 'readwrite');
            await tx.objectStore(STORE_MODELS).put({
                buffer: buffer,
                contentType: contentType || (key.endsWith('.json') ? 'application/json' : 'application/octet-stream'),
                timestamp: Date.now()
            }, key);
        } catch (e) {
            console.warn('[FaceModelCache] Failed to save to IndexedDB:', key, e);
        }
    }

    // Transparent Fetch Interceptor for /models/*
    const originalFetch = window.fetch;
    window.fetch = async function (input, init) {
        let url = '';
        if (typeof input === 'string') {
            url = input;
        } else if (input && input.url) {
            url = input.url;
        }

        // Only intercept face-api model requests
        if (url && (url.includes('/models/') || url.includes('model-shard') || url.includes('model-weights_manifest.json'))) {
            let cleanKey = url.split('?')[0];
            try {
                cleanKey = (new URL(cleanKey, window.location.origin)).pathname;
            } catch(e) {}
            const cached = await getCachedModelFile(cleanKey);
            if (cached && cached.buffer) {
                // Instantly return from client-side IndexedDB!
                return new Response(cached.buffer.slice(0), {
                    status: 200,
                    statusText: 'OK',
                    headers: {
                        'Content-Type': cached.contentType,
                        'X-Face-Cache': 'HIT-INDEXEDDB'
                    }
                });
            }

            // Not in cache, fetch from network and cache for next time
            try {
                const response = await originalFetch.apply(this, arguments);
                if (response.ok) {
                    const cloned = response.clone();
                    cloned.arrayBuffer().then(buffer => {
                        const contentType = response.headers.get('Content-Type') || (cleanKey.endsWith('.json') ? 'application/json' : 'application/octet-stream');
                        saveModelFile(cleanKey, buffer, contentType);
                    }).catch(() => {});
                }
                return response;
            } catch (err) {
                throw err;
            }
        }

        return originalFetch.apply(this, arguments);
    };

    // Load model with Net in-memory check and fast fallback
    async function loadModelWithCache(net, modelPath) {
        if (!net) return false;

        // In-memory check: if already loaded in this session, return instantly
        if (net.isLoaded || (net.params && Object.keys(net.params).length > 0)) {
            console.log(`[FaceModelCache] Model already initialized in memory: ${net._name || 'net'}`);
            return true;
        }

        try {
            await net.loadFromUri(modelPath);
            return true;
        } catch (error) {
            console.error(`[FaceModelCache] Failed to load net from ${modelPath}:`, error);
            throw error;
        }
    }

    // Background preloader for dashboard / idle time
    async function preloadFaceModels(modelBaseUrl = '/models') {
        const files = [
            'tiny_face_detector_model-weights_manifest.json',
            'tiny_face_detector_model-shard1',
            'face_landmark_68_model-weights_manifest.json',
            'face_landmark_68_model-shard1',
            'face_recognition_model-weights_manifest.json',
            'face_recognition_model-shard1',
            'face_recognition_model-shard2'
        ];

        console.log('[FaceModelCache] Starting background preload of model shards...');
        for (const file of files) {
            let url = `${modelBaseUrl}/${file}`;
            try {
                url = (new URL(url, window.location.origin)).pathname;
            } catch(e) {}
            const cached = await getCachedModelFile(url);
            if (!cached) {
                try {
                    const res = await originalFetch(url);
                    if (res.ok) {
                        const buffer = await res.arrayBuffer();
                        const contentType = res.headers.get('Content-Type') || (file.endsWith('.json') ? 'application/json' : 'application/octet-stream');
                        await saveModelFile(url, buffer, contentType);
                        console.log(`[FaceModelCache] Preloaded & cached: ${file}`);
                    }
                } catch (e) {
                    console.warn(`[FaceModelCache] Failed to preload ${file}:`, e);
                }
            }
        }
        console.log('[FaceModelCache] Background model preload complete.');
        sessionStorage.setItem('faceModelsPreloaded', 'true');
        return true;
    }

    // Save Descriptors (Float32Array converted to plain arrays for structured clone)
    async function saveDescriptors(nik, descriptors, wajahFiles, wajahSig) {
        try {
            const db = await getDB();
            const tx = db.transaction([STORE_DESCRIPTORS], 'readwrite');
            const plainDescriptors = descriptors.map(d => Array.from(d));
            await tx.objectStore(STORE_DESCRIPTORS).put({
                descriptors: plainDescriptors,
                wajahFiles: wajahFiles || [],
                wajahSig: wajahSig || (wajahFiles ? wajahFiles.join(',') : Date.now().toString()),
                timestamp: Date.now()
            }, nik);
            console.log(`[FaceModelCache] Descriptors for ${nik} saved to cache`);
        } catch (e) {
            console.warn(`[FaceModelCache] Failed to save descriptors for ${nik}:`, e);
        }
    }

    // Load Descriptors (Plain arrays converted back to Float32Array)
    async function loadDescriptors(nik) {
        try {
            const db = await getDB();
            return new Promise((resolve) => {
                const tx = db.transaction([STORE_DESCRIPTORS], 'readonly');
                const req = tx.objectStore(STORE_DESCRIPTORS).get(nik);
                req.onsuccess = () => {
                    const result = req.result;
                    if (result && (Date.now() - result.timestamp) < CACHE_EXPIRY && Array.isArray(result.descriptors)) {
                        const floatDescriptors = result.descriptors.map(arr => new Float32Array(arr));
                        resolve({
                            descriptors: floatDescriptors,
                            wajahFiles: result.wajahFiles,
                            wajahSig: result.wajahSig,
                            timestamp: result.timestamp
                        });
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = () => resolve(null);
            });
        } catch (e) {
            return null;
        }
    }

    async function clearDescriptors(nik) {
        try {
            const db = await getDB();
            const tx = db.transaction([STORE_DESCRIPTORS], 'readwrite');
            await tx.objectStore(STORE_DESCRIPTORS).delete(nik);
            console.log(`[FaceModelCache] Descriptors cleared for ${nik}`);
            return true;
        } catch (e) {
            return false;
        }
    }

    async function clearCache() {
        try {
            const db = await getDB();
            const tx = db.transaction([STORE_MODELS, STORE_DESCRIPTORS], 'readwrite');
            await tx.objectStore(STORE_MODELS).clear();
            await tx.objectStore(STORE_DESCRIPTORS).clear();
            console.log('[FaceModelCache] Entire face cache cleared');
            return true;
        } catch (e) {
            return false;
        }
    }

    window.FaceModelCache = {
        loadModelWithCache,
        preloadFaceModels,
        loadDescriptors,
        saveDescriptors,
        clearDescriptors,
        clearCache,
        initDB: getDB
    };

    console.log('[FaceModelCache] High-performance model caching engine active');
})();
