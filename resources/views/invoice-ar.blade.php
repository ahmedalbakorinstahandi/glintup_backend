<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فاتورة ضريبية</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

    body {
      font-family: 'Cairo', sans-serif;
      direction: rtl;
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
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
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

    .meta, .items, .totals, .footer {
      margin-bottom: 20px;
    }

    .meta div, .totals div {
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

    table, th, td {
      border: 1px solid #ccc;
    }

    th, td {
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

    /* PDF Download Buttons */
    .pdf-actions {
      text-align: center;
      margin-bottom: 20px;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #dee2e6;
    }

    .pdf-btn {
      display: inline-block;
      padding: 10px 20px;
      margin: 0 10px;
      background-color: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .pdf-btn:hover {
      background-color: #c82333;
      color: white;
      text-decoration: none;
    }

    .pdf-btn.secondary {
      background-color: #6c757d;
    }

    .pdf-btn.secondary:hover {
      background-color: #5a6268;
    }

    .language-switch {
      margin-top: 10px;
    }

    .language-switch a {
      color: #007bff;
      text-decoration: none;
      margin: 0 10px;
    }

    .language-switch a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <!-- PDF Download Actions -->
    <div class="pdf-actions">
      <a href="{{ $pdf_url ?? '#' }}" class="pdf-btn" target="_blank">
        📄 تحميل PDF
      </a>
      <a href="{{ $pdf_url ?? '#' }}" class="pdf-btn secondary" download>
        💾 حفظ PDF
      </a>
      <div class="language-switch">
        <a href="{{ url('/invoices/' . ($invoice_code ?? '') . '/en') }}">English</a>
        <a href="{{ url('/invoices/' . ($invoice_code ?? '') . '/ar') }}">العربية</a>
      </div>
    </div>

    <div class="header">
      <img src="{{ $logo }}" alt="شعار التطبيق" class="logo">
      <h1>فاتورة ضريبية</h1>
      <div style="font-size: 12px;">نسخة إلكترونية - لا تحتاج توقيع</div>
    </div>

    <div class="meta">
      <div><span class="bold">اسم التطبيق:</span> {{ $app_name }}</div>
      <div><span class="bold">رقم الفاتورة:</span> {{ $invoice['number'] }}</div>
      <div><span class="bold">التاريخ:</span> {{ $invoice['date'] }}</div>
      <div><span class="bold">الوقت:</span> {{ $invoice['time'] }}</div>
      <div><span class="bold">اسم العميل:</span> {{ $customer['name'] }}</div>
      <div><span class="bold">رقم الحجز:</span> {{ $invoice['booking_number'] ?? 'لا يوجد' }}</div>
      <div><span class="bold">مزود الخدمة:</span> {{ $salon['name'] }} - {{ $salon['provider_type'] }}</div>
      <div><span class="bold">الرقم الضريبي:</span> {{ $salon['tax_number'] }}</div>
    </div>

    <div class="items">
      <table>
        <thead>
          <tr>
            <th>م</th>
            <th>الخدمة</th>
            <th>المدة</th>
            <th>السعر ({{ $currency }})</th>
          </tr>
        </thead>
        <tbody>
          @foreach($services as $index => $service)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $service['name'] }}</td>
            <td>{{ $service['duration'] }} دقيقة</td>
            <td>{{ number_format($service['discounted_price'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top: 8px;"><span class="bold">عدد الخدمات:</span> {{ count($services) }}</div>
    </div>

    <div class="totals">
      <div><span class="bold">المجموع الفرعي:</span> {{ number_format($invoice['total_before_discount'], 2) }} {{ $currency }}</div>
      <div><span class="bold">الخصم:</span> {{ number_format($invoice['coupon_discount'], 2) }} {{ $currency }}</div>
      <!-- <div><span class="bold">الضريبة (5٪):</span> {{ number_format(($invoice['total_after_discount'] * 0.05), 2) }} {{ $currency }}</div> -->
      <div><span class="bold">الإجمالي:</span> {{ number_format($invoice['total_after_discount'], 2) }} {{ $currency }}</div>
      <div><span class="bold">حالة الدفع:</span> {{ $invoice['payment_status'] }}</div>
    </div>

    @if(!empty($invoice['notes']))
    <div style="font-size: 12px; margin-top: 10px;">
      <span class="bold">ملاحظات العميل:</span><br>
      {{ $invoice['notes'] }}
    </div>
    @endif

    <div class="paid-stamp">✔ تم الدفع بالكامل</div>

    <div class="qr">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url('/invoices/'.$invoice['number'].'/ar') }}" alt="QR Code">
      <div style="font-size:11px;">رمز الفاتورة</div>
    </div>

    <div class="footer">
      <p>{{ $footer['thanks_message'] }}</p>
      <p>{{ $footer['cancel_policy'] }}</p>
      <p>{{ $footer['service_policy'] }}</p>
      <p>تم إصدار هذه الفاتورة تلقائيًا من النظام</p>
    </div>
  </div>
</body>
</html>