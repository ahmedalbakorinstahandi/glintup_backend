<?php


namespace App\Http\Services\Users;

use App\Http\Permissions\Users\ContactPermission;
use App\Models\Users\Contact;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Services\PhoneService;

class ContactService
{
    public  function index($data)
    {
        $query = Contact::query();


        $query = ContactPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['name', 'phone'],
            [],
            ['created_at'],
            [],
            ['id']
        );
    }

    public  function show($id)
    {
        $item = Contact::where('id', $id)->first();


        if (!$item) {
            MessageService::abort(404, 'messages.contact.item_not_found');
        }
        return $item;
    }

    public  function create($data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $data['phone_code'] = $countryCode;
        $data['phone'] = $phoneNumber;


        // if avatar is null  , make image as first letter of name
        // if (empty($data['avatar'])) {
        //     $data['avatar'] = 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&size=256&background=random';
        // }

        $contact = Contact::create($data);

        return $contact;
    }

    public  function update(Contact $item, $data)
    {


        if (isset($data['phone'])) {
            $phoneParts = PhoneService::parsePhoneParts($data['phone']);
            $countryCode = $phoneParts['country_code'];
            $phoneNumber = $phoneParts['national_number'];

            $data['phone_code'] = $countryCode;
            $data['phone'] = $phoneNumber;
        }


        $item->update($data);


        return $item;
    }

    public  function delete(Contact $item)
    {
        return $item->delete();
    }
}
