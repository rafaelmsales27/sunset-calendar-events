<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use DatePeriod;
use DateTimeZone;

class IndexController extends Controller
{
    public function generate(Request $request)
    {
        // get current time zone (for now don't let user choose)
        $timezone = $request->session();
        // $tz = new DateTimeZone($timezone);
        // $tzData = $tz->getLocation();
        // get city
        // extract lat lng from city

        // divide period into days
        $start = $request->input('start');
        $end = $request->input('end');

        $start = new DateTime($start);
        $end = new DateTime($end);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);

        // TODO including present day and final day
        // TODO ask if should include start and end date into events

        foreach ($period as $day) {
            //     - search for sunset
            //     - create event in .ics for that day
        }

        // TODO Manage api errors (toast?)
        // $response = $this->sunsetApiCall('38.907192', '-77.036873', 'UTC', '1990-05-22');

        return view('index', [
            'timezone' => $timezone,
            'start' => $start,
            'end' => $end,
        ]);

        // Download file
        // return response()->download($pathToFile, $fileName);
    }

    private function sunsetApiCall(string $lat, string $lng, string $timezone = 'UTC', string $date = 'today'): string
    {
        // Build the URL with query parameters
        $url = "https://api.sunrisesunset.io/json?" . http_build_query([
            'lat' => $lat,
            'lng' => $lng,
            'timezone' => $timezone,
            'date' => $date
        ]);

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        // Execute the request and capture the response
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        return $response;
    }
}
