<?php


namespace App\Http\Services\General;

use App\Http\Permissions\General\AddressPermission;
use App\Models\General\Address;
use App\Services\FilterService;
use App\Services\LocationService;
use App\Services\MessageService;

class AddressService
{
    // index
    public function index($data)
    {
        $query = Address::query();

        $query = AddressPermission::filterIndex($query);

        $query = FilterService::applyFilters(
            $query,
            $data,
            ['name', 'address', 'address_secondary', 'city', 'country', 'postal_code'],
            ['latitude', 'longitude'],
            ['addressable_id', 'addressable_type'],
        );

        return $query;
    }

    // show
    public function show($id)
    {
        $address = Address::find($id);


        if (!$address) {
            MessageService::abort(404, 'messages.address.not_found');
        }

        $address->load('addressable');

        return $address;
    }

    // create
    public function create($data)
    {

        $locationData = LocationService::getLocationData($data['latitude'], $data['longitude']);

        $data['address'] = $locationData['address'];
        $data['city'] = $locationData['city'];
        $data['country'] = $locationData['country'];
        $data['postal_code'] = $locationData['postal_code'];
        $data['address_secondary'] = $locationData['address_secondary'];
        $data['latitude'] = $locationData['latitude'];
        $data['longitude'] = $locationData['longitude'];
        $data['city_place_id'] = $locationData['city_place_id'];


        $address = Address::create($data);

        $address->load('addressable');

        return $address;
    }

    // delete
    public function delete($address)
    {
        $address->delete();

        return $address;
    }
}
