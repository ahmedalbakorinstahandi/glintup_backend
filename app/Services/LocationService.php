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

            if (!$emiratePlaceId) {
                MessageService::abort(400, 'messages.location.invalid_location');
            }

            $addressComponents = $googleMapData['address_components'];
            $city = '';
            $country = '';
            $postalCode = '';
            $addressSecondary = '';

            foreach ($addressComponents as $component) {
                if (in_array('locality', $component['types'])) {
                    $city = $component['long_name'];
                }
                if (in_array('country', $component['types'])) {
                    $country = $component['long_name'];
                }
                if (in_array('postal_code', $component['types'])) {
                    $postalCode = $component['long_name'];
                }
                if (in_array('sublocality', $component['types'])) {
                    $addressSecondary = $component['long_name'];
                }
            }

            return [
                'address' => $googleMapData['formatted_address'] ?? '',
                'city' => $city,
                'country' => $country,
                'postal_code' => $postalCode,
                'address_secondary' => $addressSecondary,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city_place_id' => $emiratePlaceId,
            ];
        }

        return null;
    }
}
