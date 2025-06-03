<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة ضريبية مبسطة</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            direction: rtl;
            width: 300px;
            margin: auto;
            background: #fff;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px 0; text-align: right; }
        .logo { height: 50px; margin-bottom: 5px; }
        .en { direction: ltr; text-align: left; }
        .notes { font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="center">
        <img src="https://i.ibb.co/zH8KXpy6/Group-3.png" alt="GlintUp" class="logo">
        <div class="bold">GlintUp</div>
        <div>فاتورة ضريبية مبسطة</div>
        <div class="en">Tax Invoice</div>
        <div class="bold">{{ $salon->name }}</div>
        <div>{{ $salon->provider_type }}</div>
    </div>

    <div class="line"></div>

    <table>
        <tr><td>الموقع / Location:</td><td class="en">{{ $salon->address }}</td></tr>
        <tr><td>رقم الفاتورة / Invoice:</td><td class="en">{{ $invoice->number }}</td></tr>
        <tr><td>رقم الحجز / Booking:</td><td class="en">{{ $invoice->booking_number ?? 'N/A' }}</td></tr>
        <tr><td>التاريخ / Date:</td><td class="en">{{ $invoice->date }}</td></tr>
        <tr><td>الوقت / Time:</td><td class="en">{{ $invoice->time }}</td></tr>
        <tr><td>العميل / Customer:</td><td class="en">{{ $customer->name }}</td></tr>
        <tr><td>رقم العميل / Phone:</td><td class="en">{{ $customer->phone }}</td></tr>
        <tr><td>الرقم الضريبي / Tax:</td><td class="en">{{ $invoice->tax_number }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الخدمة</th>
                <th>المدة</th>
                <th>السعر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->services as $index => $service)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $service->name }}</td>
                <td>{{ $service->duration }} دقيقة</td>
                <td class="en">{{ number_format($service->discounted_price, 2) }} {{ $currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr><td>المجموع / Subtotal:</td><td class="en">{{ number_format($invoice->total_before_discount, 2) }} {{ $currency }}</td></tr>
        <tr><td>الخصم / Discount:</td><td class="en">{{ number_format($invoice->coupon_discount, 2) }} {{ $currency }}</td></tr>
        <tr><td>الضريبة / VAT 5%:</td><td class="en">{{ number_format(($invoice->total_after_discount * 0.05), 2) }} {{ $currency }}</td></tr>
        <tr class="bold"><td>الإجمالي / Total:</td><td class="en">{{ number_format($invoice->total_after_discount * 1.05, 2) }} {{ $currency }}</td></tr>
        <tr><td>حالة الدفع / Status:</td><td class="en">{{ $invoice->payment_status }}</td></tr>
    </table>

    <div class="line"></div>

    @if(isset($invoice->notes))
    <div class="notes">
        <div class="bold">ملاحظات / Notes:</div>
        <div>{{ $invoice->notes }}</div>
    </div>
    <div class="line"></div>
    @endif

    <div class="center">
        <p style="font-size: 10px">
            THANKS FOR USING GLINTUP!<br>
            REFUND WITHIN 3 DAYS - EXCHANGE WITHIN 14 DAYS<br>
            NO RETURN ON SERVICES AFTER EXECUTION.
        </p>
        <div class="en">{{ $invoice->number }}</div>
    </div>

</body>
</html>
