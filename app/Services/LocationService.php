<?php


namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;


class LocationService
{

    public static function getLocationData($latitude, $longitude)
    {
        // $apiKey = env('GOOGLE_MAPS_API_KEY');
        $apiKey = 'AIzaSyCkMlal5E0x_tV7q0AtwP8hLA_XJQBwSfo';

        $language = request()->header('Accept-Language') ?? 'en';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}&language={$language}";

        $response = file_get_contents($url);
        $responseData = json_decode($response, true);

        if ($responseData['status'] == 'OK') {
            $googleMapData = $responseData['results'][0];
            $emiratePlaceId = null;

            foreach ($responseData['results'] as $result) {
                if (in_array('administrative_area_level_1', $result['types']) && in_array('political', $result['types'])) {
                    $emiratePlaceId = $result['place_id'];
                    break;
                }
            }

            if(!$emiratePlaceId) {
                MessageService::abort(400, 'messages.invalid_location');
            }

            return [
                'address' => $googleMapData['formatted_address'] ?? '',
                'city' => $googleMapData['address_components']['locality'] ?? '',
                'country' => $googleMapData['address_components']['country'] ?? '',
                'postal_code' => $googleMapData['address_components']['postal_code'] ?? '',
                'address_secondary' => $googleMapData['address_components']['sublocality'] ?? '',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'emirate_place_id' => $emiratePlaceId,
            ];
        }

        return null;
    }
}
