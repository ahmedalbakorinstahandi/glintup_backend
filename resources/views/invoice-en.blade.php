<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            direction: ltr;
            font-size: 14px;
            max-width: 650px;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        .invoice-box {
            background: #fff;
            padding: 20px 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img.logo {
            width: 80px;
            margin-bottom: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #444;
        }

        .meta,
        .items,
        .totals,
        .footer {
            margin-bottom: 20px;
        }

        .meta div,
        .totals div {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background: #f0f0f0;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .paid-stamp {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #0a8f33;
            border: 2px dashed #0a8f33;
            padding: 8px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .qr {
            text-align: center;
            margin-top: 15px;
        }

        .qr img {
            width: 100px;
        }

        .footer {
            font-size: 12px;
            text-align: center;
            color: #666;
        }

        .footer p {
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <img src="{{ $logo }}" alt="App Logo" class="logo">
            <h1>Tax Invoice</h1>
            <div style="font-size: 12px;">Electronic copy - no signature required</div>
        </div>

        <div class="meta">
            <div><span class="bold">App Name:</span> {{ $app_name }}</div>
            <div><span class="bold">Invoice Number:</span> {{ $invoice['number'] }}</div>
            <div><span class="bold">Date:</span> {{ $invoice['date'] }}</div>
            <div><span class="bold">Time:</span> {{ $invoice['time'] }}</div>
            <div><span class="bold">Customer:</span> {{ $customer['name'] }}</div>
            <div><span class="bold">Phone:</span> {{ $customer['phone'] }}</div>
            <div><span class="bold">Booking Number:</span> {{ $invoice['booking_number'] }}</div>
            <div><span class="bold">Service Provider:</span> {{ $salon['name'] }} - {{ $salon['provider_type'] }}</div>
            <div><span class="bold">Tax Number:</span> {{ $salon['tax_number'] }}</div>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service</th>
                        <th>Duration</th>
                        <th>Price ({{ $currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $service['name'] }}</td>
                        <td>{{ $service['duration'] }} min</td>
                        <td>{{ number_format($service['discounted_price'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 8px;"><span class="bold">Total Services:</span> {{ count($services) }}</div>
        </div>

        <div class="totals">
            <div><span class="bold">Subtotal:</span> {{ number_format($invoice['total_before_discount'], 2) }} {{ $currency }}</div>
            <div><span class="bold">Discount:</span> {{ number_format($invoice['coupon_discount'], 2) }} {{ $currency }}</div>
            <!-- <div><span class="bold">VAT (5%):</span> {{ number_format($invoice['total_after_discount'] * 0.05, 2) }} {{ $currency }}</div> -->
            <div><span class="bold">Total:</span> {{ number_format($invoice['total_after_discount'] * 1.05, 2) }} {{ $currency }}</div>
            <div><span class="bold">Payment Status:</span> {{ $invoice['payment_status'] }}</div>
        </div>

        <div class="paid-stamp">✔ Fully Paid</div>

        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url('/invoices/'.$invoice['number'].'/en') }}" alt="QR Code">
            <div style="font-size:11px;">Invoice QR Code</div>
        </div>

        @if(!empty($invoice['notes']))
        <div style="font-size: 12px; margin-top: 10px;">
            <span class="bold">Customer Notes:</span><br>
            {{ $invoice['notes'] }}
        </div>
        @endif

        <div class="footer">
            <p>{{ $footer['thanks_message'] }}</p>
            <p>{{ $footer['cancel_policy'] }}</p>
            <p>{{ $footer['service_policy'] }}</p>
            <p>This invoice was generated automatically by the system</p>
        </div>
    </div>
</body>

</html>