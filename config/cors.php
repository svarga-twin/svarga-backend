<?php

// SVARGA — konfigurasi CORS supaya browser (React di localhost:5173, atau
// domain produksi svarga-app nanti) diizinkan memanggil API Laravel ini.
// Timpa isi config/cors.php bawaan Laravel dengan ini, atau cukup sesuaikan
// 'paths' dan 'allowed_origins'-nya.

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173', // svarga-app (Vite dev server)
        // Tambahkan domain produksi svarga-app di sini nanti, mis:
        // 'https://svarga-app.vercel.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
