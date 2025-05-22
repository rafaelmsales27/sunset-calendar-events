<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use DatePeriod;
use DateTimeZone;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Carbon\Carbon;

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
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $start = new DateTime($start);
        $end = new DateTime($end);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end, DatePeriod::INCLUDE_END_DATE);

        // TODO including present day and final day
        // TODO ask if should include start and end date into events

        $sunData = [];
         foreach ($period as $dt) {
            $currentDate = $dt->format('Y-m-d');
            if (!$latitude || !$longitude) {
                return redirect()->route('index');
            }
            $response = $this->sunsetApiCall($latitude, $longitude, $currentDate);
            $response = json_decode($response, true);
            // TODO Manage api errors (toast?)
            if ($response['status'] !== 'OK') {
                return view('index', [
                    'timezone' => $timezone,
                    'start' => $start,
                    'end' => $end,
                    'test' => json_encode($sunData),
                ]);
            }
            $sunData[] = $response;
        }

        if (!$sunData) {
            return view('index', [
                'timezone' => $timezone,
                'start' => $start,
                'end' => $end,
                'test' => json_encode($sunData),
            ]);
        }
        $events = [];
        foreach ($sunData as $day) {
            //     - search for sunset
            $eventTime = $day['results']['sunset']; //HH:MM:SS AM/PM
            $eventDate = $day['results']['date']; //Y-m-d
            $apiTimezone = $day['results']['timezone'];
            $combinedDateTimeString = $eventDate . ' ' . $eventTime;
            // Parse into a Carbon instance, explicitly setting the timezone from the API
            $eventStartTime = Carbon::parse($combinedDateTimeString, $apiTimezone);
            // Clone the start time and add 30 minutes for the end time
            $eventEndTime = $eventStartTime->copy()->addMinutes(30);
            // $eventEndTime = + 30 min ?
            //     - create event in .ics for that day
            $events[] = Event::create()
                        ->name('Sunset at timezone ' . $apiTimezone)
                        // ->uniqueIdentifier('A unique identifier can be set here')
                        ->createdAt(Carbon::now())
                        ->startsAt($eventStartTime)
                        ->endsAt($eventEndTime)
                        // ->transparent()
                        // ->alertMinutesBefore(30, 'Sunset will start in 30 minutes')
                        ->coordinates($latitude, $longitude);
        }

        if (empty($events)) {
            return redirect()->route('index')->with('error', 'No calendar events could be generated, even after retrieving sunset data. Please check logs.');
        }



        $calendar = Calendar::create()
        ->name('Laracon Online')
        ->description('Auto-generated sunset events for your chosen location.')
        ->event($events);

        $fileName = 'sunset_events_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.ics';

        return response($calendar->get()) // Call ->get() to generate the ICS string
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        
        // $response = $this->sunsetApiCall('38.907192', '-77.036873', 'UTC', '1990-05-22');

        // return view('index', [
        //     'timezone' => $timezone,
        //     'start' => $start,
        //     'end' => $end,
        //     'test' => json_encode($sunData),
        // ]);

        // Download file
        // return response()->download($pathToFile, $fileName);
    }

    // private function sunsetApiCall(string $lat, string $lng, string $timezone = 'UTC', string $date = 'today'): string
    private function sunsetApiCall(string $lat, string $lng, string $date = 'today'): string
    {
        // Build the URL with query parameters
        $url = "https://api.sunrisesunset.io/json?" . http_build_query([
            'lat' => $lat,
            'lng' => $lng,
            // 'timezone' => $timezone,
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
