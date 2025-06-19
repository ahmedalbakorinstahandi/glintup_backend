<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Services\Invoice\InvoiceDefaultData;
use App\Models\Booking\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(Request $request, $id, $lang = null)
    {
        // Get language preference (default to English if not specified)
        if (!$lang) {
            $lang = $request->header('Accept-Language', 'en');
        }

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        // Try to find the invoice
        $invoice = Invoice::where('code', $id)->orWhere('id', $id)->first();

        // If invoice not found, return the appropriate error view
        if (!$invoice) {
            return view("invoice-not-found-{$lang}");
        }

        // Get invoice data
        $data = InvoiceDefaultData::getDefaultData($invoice, $lang);

        // Return the appropriate view based on language
        return view("invoice-{$lang}", $data);
    }
}
