<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Services\Invoice\InvoiceDefaultData;
use App\Models\Booking\Invoice;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

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

    public function showPdf(Request $request, $id, $lang = null)
    {
        $invoice = Invoice::with('serviceRequest.client.user')->findOrFail($id);

        $html = view('invoices.invoice', compact('invoice'))->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('framework/cache'),
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0
        ]);

        $mpdf->WriteHTML($html);

        // Return PDF directly without saving to storage
        return response($mpdf->Output('', \Mpdf\Output\Destination::STRING))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice_' . $invoice->code . '.pdf"');
    }
}
