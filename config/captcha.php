<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the captcha. The default settings
    | should work for most cases. You can adjust the settings as needed.
    |
    */

    'length' => 5, // Panjang captcha
    'width' => 200, // Lebar gambar captcha
    'height' => 100, // Tinggi gambar captcha
    'quality' => 90, // Kualitas gambar
    'math' => true, // Menggunakan captcha matematika
    'fonts' => [], // Font yang digunakan
    'bgImage' => false, // Gambar latar belakang
    'bgColor' => [255, 255, 255], // Warna latar belakang
    'overlay' => false, // Overlay pada captcha
    'overlayColor' => [0, 0, 0], // Warna overlay
    'overlayOpacity' => 0.3, // Opasitas overlay
    'cachePath' => storage_path('app/captcha'), // Lokasi cache captcha
];
