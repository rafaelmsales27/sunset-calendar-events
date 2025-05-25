<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Intl\Countries;

Artisan::command('geonames:parse-countries', function () {
    $filePath = storage_path('app/private/geonames/cities1000.txt');

    if (!file_exists($filePath)) {
        $this->error("File not found: $filePath");
        return;
    }

    $countries = [];

    $handle = fopen($filePath, 'r');

    while (($line = fgets($handle)) !== false) {
        $parts = explode("\t", $line);
        $countryCode = $parts[8] ?? null;

        if ($countryCode) {
            $countries[$countryCode] = true;
        }
    }

    fclose($handle);

    $uniqueCountries = array_keys($countries);
    sort($uniqueCountries);

    Storage::put('geonames/countries.json', json_encode($uniqueCountries, JSON_PRETTY_PRINT));

    $this->info("Saved " . count($uniqueCountries) . " unique country codes to geonames/countries.json.");
});

