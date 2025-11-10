<?php
// money-order.php
include_once __DIR__ . '/../includes/functions.php';

// Get id from querystring
$id = $_GET['id'] ?? null;
$orders = read_json(__DIR__ . '/../data/orders.json');
$order = null;
foreach($orders as $o){
    if((string)$o['id'] === (string)$id){
        $order = $o;
        break;
    }
}
if(!$order){
    die('Invalid ID');
}

$products = read_json(__DIR__ . '/../data/products.json');
$pname = '';
foreach($products as $p){
    if((string)$p['id'] === (string)$order['product_id']){
        $pname = $p['name'];
        break;
    }
}

// Fallbacks for safety
$order_no = htmlspecialchars($order['order_no'] ?? '');
$date = htmlspecialchars($order['date'] ?? date('Y-m-d'));
$qty = (int)($order['qty'] ?? 1);
$unit_price = htmlspecialchars($order['unit_price'] ?? '0');
$total = htmlspecialchars($order['total'] ?? ($qty * ($order['unit_price'] ?? 0)));
$remarks = htmlspecialchars($order['remarks'] ?? '');
$customer_name = htmlspecialchars($order['customer']['name'] ?? '');
$customer_contact = htmlspecialchars($order['customer']['contact'] ?? '');
$customer_address = nl2br(htmlspecialchars($order['customer']['address'] ?? ''));

?>
<!doctype html>
<html lang="ur">
<head>
  <meta charset="utf-8">
  <title>Money Order — Pakistan Post</title>

  <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
  <style>
    @page { size: A4; margin: 10mm; }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: #fff;
      font-size: 13px;
      line-height: 1.3;
    }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    .a4 {
      width: 210mm;
      padding: 10mm 15mm;
      margin: 0 auto;
      background: #fff;
      position: relative;
    }

    .a4 + .a4 {
      margin-top: 0;
    }

    .mo-form {
      width: 100%;
      position: relative;
      border: 2px dashed #333;
      padding: 15px 70px 15px 20px;
      background: #fff;
      margin-bottom: 15px;
      overflow: hidden;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
      padding-bottom: 0;
      position: relative;
    }

    .mo-code {
      font-size: 13px;
      font-weight: bold;
    }

    .logo-box {
      background: #e41e26;
      padding: 5px 10px;
      display: inline-flex;
      align-items: center;
      height: 30px;
      min-width: 120px;
      justify-content: center;
    }

    .logo-box img {
      height: 20px;
      width: auto;
    }

    .mo-title {
      font-size: 18px;
      font-weight: bold;
      text-decoration: underline;
      margin-left: 30px;
      letter-spacing: 0.5px;
    }
    .header-line { border-top: 1px solid #000; margin: 6px 0 10px; }

    .denominations {
      position: absolute;
      right: 10px;
      top: 40px;
      display: flex;
      flex-direction: column;
      gap: 3px;
      z-index: 10;
    }

    .denom-circle {
      width: 42px;
      height: 42px;
      border: 2px solid #d32f2f;
      color:red;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 11px;
      background: #fff;
      line-height: 1.1;
      text-align: center;
      padding: 2px;
      position: relative;
      right: 0;
    }

    .form-row {
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
      direction: rtl;
      padding-right: 0;
    }

    .form-row.ltr {
      direction: ltr;
    }

    .form-label {
      font-size: 13px;
      white-space: nowrap;
    }

    .form-field {
      flex: 1;
      border-bottom: 1px solid #000;
      min-height: 18px;
      padding: 0 5px;
      font-size: 12px;
    }

    .urdu-text {
      font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', Arial;
      font-size: 14px;
      direction: rtl;
    }

    .stamp-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 20px 0;
      padding: 10px 0;
    }

    .round-stamp {
      width: 80px;
      height: 80px;
      border: 2px solid #000;
      border-radius: 50%;
      background: #f9f9f9;
    }

    .oblong-stamp {
      width: 200px;
      height: 60px;
      border: 2px solid #000;
      background: #f9f9f9;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
    }

    .footer-section {
      border-top: 2px dashed #333;
      margin-top: 15px;
      padding-top: 10px;
    }

    .signature-row {
      display: flex;
      justify-content: space-between;
      margin: 10px 0;
    }

    .signature-field {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .signature-line {
      border-bottom: 1px solid #000;
      height: 1px;
      display: inline-block;
    }

    .bottom-boxes {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      padding: 10px 0;
    }

    .info-box {
      border: 1px solid #000;
      padding: 5px;
    }

    .circle-stamp {
      width: 60px;
      height: 60px;
      border: 2px solid #000;
      border-radius: 50%;
    }

    .receipt-boxes {
      display: flex;
      gap: 0;
      margin-top: 10px;
    }

    .receipt-box {
      width: 30px;
      height: 30px;
      border: 1px solid #000;
    }

    .notation-text {
      font-size: 11px;
      margin-top: 8px;
      text-align: center;
      direction: rtl;
    }

    /* Instruction block (front center) */
    .inst-block { direction: rtl; text-align: right; font-size: 12px; line-height: 1.6; }
    .inst-block .solid { border-top: 1px solid #333; margin: 6px 0 6px; }
    .inst-block .dashed { border-top: 2px dashed #333; margin: 8px 0; }
    .inst-block .line { margin: 2px 0; }
    .inst-heading { font-weight: 700; display: inline-block; margin-left: 8px; }

    /* Page 2 Styles */
    .green-section {
      background: #cfead4;
      border: 2px solid #4caf50;
      padding: 10px;
      margin-bottom: 15px;
    }

    .green-box {
      border: 1px solid #2e7d32;
      background: rgba(200, 230, 201, 0.5);
    }

    .table-main {
      width: 100%;
      border-collapse: collapse;
    }

    .table-main td {
      border: 1px solid #333;
      padding: 6px;
    }

    .table-main tr:first-child td {
      background: #e8f5e9;
      font-weight: bold;
    }

    .side-instruction-box {
      border: 2px solid #333;
      padding: 8px;
      margin-bottom: 8px;
      background: #f5f5f5;
    }

    /* Back page building blocks */
    .back-lines .line {
      border-bottom: 1px solid #333;
      height: 20px;
      margin-bottom: 8px;
    }

    .back-right {
      width: 170px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .back-right .pill {
      border: 2px solid #333;
      padding: 10px 8px;
      text-align: center;
    }

    .back-squares {
      display: flex;
      gap: 0;
      align-items: center;
    }

    .back-squares .sq {
      width: 28px;
      height: 28px;
      border: 1px solid #333;
    }

    /* Green writing area inside body (scan-style) */
    .green-writing {
      background: repeating-linear-gradient(90deg, #38c077 0 18px, #30b26d 18px 36px);
      border: 2px solid #333;
      padding: 8px 10px 10px;
      position: relative;
    }
    .green-writing .rule { height: 22px; border-bottom: 1px solid #333; }
    .receipt-inline { display: flex; align-items: center; gap: 6px; margin: 6px 0; max-width: 60%; }
    .receipt-inline .sq { width: 24px; height: 24px; border: 1px solid #333; background: #fff; }
    .receipt-inline .label { font-size: 11px; color: #b71c1c; }

    /* Amount panel (top-right) */
    .green-writing .amount { position: absolute; right: 15px; top: 8px; width: 240px; direction: rtl; }
    .green-writing .amount .title { text-align: right; font-size: 12px; margin-bottom: 4px; }
    .green-writing .amounts { display:flex; justify-content:flex-end; align-items:center; gap: 30px; margin-top: 2px; direction: rtl; }
    .green-writing .segment { display:flex; flex-direction: row-reverse; align-items:center; gap: 6px; }
    .green-writing .hr.short { width: 80px; height: 1px; border-bottom: 1px solid #333; }

    .top-right-box {
      border: 2px solid #333;
      padding: 8px;
      width: 160px;
      text-align: center;
      margin-left: auto;
      background: #fff;
      margin-bottom: 0;
    }

    .instr-urdu {
      color: #b71c1c;
      text-align: center;
      font-size: 12px;
      line-height: 1.32;
      margin: 8px 0 4px 0;
    }

    .instr-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 8px;
      margin: 0;
    }

    .instr-wrap { position: relative; margin: 8px 0 28px 0; min-height: 80px; padding-top: 6px; }
    .instr-wrap .instr-urdu { text-align: center; margin: 0; }
    .instr-wrap .top-right-box { position: absolute; right: 0; top: 16px; margin: 0; }
    .instr-wrap .center-content { width: calc(100% - 240px); margin: 0 auto; padding-right: 24px; }

    /* Right-side curly bracket groups */
    .side-group { display: flex; align-items: center; gap: 8px; min-height: 70px; }
    .brace { position: relative; width: 16px; height: 100%; }
    .brace:before,
    .brace:after { content: ""; position: absolute; left: 0; width: 12px; border-left: 2px solid #333; }
    .brace:before { top: 0; height: 45%; border-top: 2px solid #333; border-top-left-radius: 10px; }
    .brace:after  { bottom: 0; height: 45%; border-bottom: 2px solid #333; border-bottom-left-radius: 10px; }

    @media print {
      .mo-form { page-break-inside: avoid; }
      body { background: #fff; }
      .a4 { min-height: 297mm; }
      .a4 + .a4 { page-break-before: always; margin-top: 0; }
    }
  </style>
</head>
<body>
  <div class="a4">
    
    <!-- Money Order Form -->
    <div class="mo-form">
      
      <!-- Header -->
      <div class="header">
        <span class="mo-code">M.O.8</span>
        <div class="logo-box">
          <img src="https://www.pakpost.gov.pk/images/New%20Logo%20PPO.jpg" alt="Pakistan Post Logo">
        </div>
        <span class="mo-title">MONEY ORDER</span>
      </div>

      <!-- Denomination circles on right -->
      <div class="denominations">
        <div class="denom-circle">2000</div>
        <div style="text-align: center;color:red">سے کم</div>
        <div class="denom-circle">4000</div>
        <div style="text-align: center;color:red">سے کم</div>
        <div class="denom-circle">6000</div>
        <div style="text-align: center;color:red">سے کم</div>
        <div class="denom-circle">8000</div>
        <div style="text-align: center;color:red">سے کم</div>
        <div class="denom-circle">10000</div>
        <div style="text-align: center;color:red">تک</div>
      </div>

      <!-- Form Fields with exact layout -->
      <div style="margin-right: 0;">
        <div class="form-row ltr" style="padding-right: 0;">
          <span class="form-label urdu-text">سب آفس</span>
          <div class="form-field" style="flex: 1;"></div>
          <span class="form-label urdu-text" >پوسٹ ماسٹر</span>
        </div>

        <div class="form-row ltr" style="padding-right: 0;">
          <span class="form-label urdu-text">ہیڈ آفس</span>
          <div class="form-field" style="flex: 1;"></div>
        </div>

        <div class="form-row ltr" style="padding-right: 0;">
          <div class="form-field" style="flex: 1;"></div>
          <span class="form-label urdu-text">رقم بھیجنے والے کا نام اور مکمل پتہ (انگریزی میں) پاکستان</span>
        </div>

        <div class="form-row ltr" style="padding-right: 0;">
          <div class="form-field" style="flex: 1;"><?= $customer_name ?></div>
          <span class="form-label urdu-text">دوسرا پوسٹ آفس/بینک/آرمی نمبر پاکستان</span>
        </div>

        <div class="form-row ltr" style="padding-right: 0; margin-top: 10px;">
          <div style="display: flex; align-items: center; width: 100%; justify-content: space-between; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="form-field" style="width: 80px;"></div>
              <span class="form-label urdu-text">پیسے</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex: 1;">
              <div class="form-field" style="flex: 1;"></div>
              <span class="form-label urdu-text">روپے</span>
            </div>
          </div>
        </div>
        <div class="form-row ltr" style="padding-right: 0; margin-top: 6px;">
          <div class="form-field" style="flex: 1;"></div>
          <span class="form-label urdu-text">(حروف میں)</span>
        </div>

        <div class="form-row ltr" style="padding-right: 0;">
          <div style="display: flex; align-items: center; gap: 15px; width: 100%;">
            <span class="form-label urdu-text">پیسے کمیشن سے کٹے</span>
            <div class="form-field" style="width: 100px;"></div>
            <span class="form-label urdu-text">چیک</span>
            <div class="form-field" style="width: 100px;"></div>
            <span class="form-label urdu-text">نمبر</span>
            <div class="form-field" style="width: 100px;"></div>
            <span class="form-label urdu-text">(کرنسی نوٹ ملیں گے)</span>
          </div>
        </div>
      </div>

      <div style="border-top: 2px dashed #333; margin: 15px 0; padding-top: 10px;">
        
        <!-- Stamp Section -->
        <div class="stamp-section" style="padding-right: 0;">
          <div style="text-align: center; width: 90px;">
            <div class="round-stamp"></div>
            <div style="font-size: 10px; margin-top: 5px;">Round M.O. Stamp<br>authorising Payment</div>
          </div>
          
          <div style="flex: 1; padding: 0 15px;">
            <div class="inst-block urdu-text">
              <div class="line">_______________________________ رقم وصول کرنے والے کے لیے ہدایات _______________________________</div>
              <div class="line">_______________________________ (براہ کرم واضح طور پر لکھیں) _______________________________</div>
              <div class="solid"></div>
              <div class="line">(١) _________________________________________________</div>
              <div class="line">(٢) _________________________________________________</div>
              <div class="line">(٣) _________________________________________________</div>
              <div class="dashed"></div>
              <div class="line"><span class="inst-heading">شناخت</span> <span class="small-note">(اگر وصول کنندہ دستخط/انگوٹھا لگائے تو شناخت مکمل کرنی ہوگی)</span></div>
            </div>
            <div class="form-row ltr" style="padding-right: 0; margin-top: 6px;">
              <div class="form-field" style="flex: 0.5;"></div>
              <span class="form-label urdu-text">دستخط وصول کنندہ</span>
              <div class="form-field" style="flex: 0.5;"><?php echo $date; ?></div>
              <span class="form-label urdu-text">تاریخ</span>
            </div>
          </div>
          <div style="text-align: center; width: 180px;">
            <div class="oblong-stamp">Oblong M.O. Stamp on Payment</div>
          </div>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
          <!-- First row with signatures and circle -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin: 10px 0;">
            <div style="display: flex; gap: 20px; flex: 1;">
              <div style="flex: 1;">
                <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                <span class="form-label urdu-text">کھاتہ دار کا نام</span>
              </div>
              <div style="flex: 1;">
                <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                <span class="form-label urdu-text">پوسٹ ماسٹر کے دستخط</span>
              </div>
            </div>
            <div style="width: 50px; height: 50px; border: 2px solid #000; border-radius: 50%; margin-left: 20px;"></div>
          </div>

          <!-- Second row with box and signatures -->
          <div style="display: flex; justify-content: space-between; align-items: start; margin: 15px 0;">
            <div class="info-box" style="width: 120px; height: 60px;"></div>
            
            <div style="flex: 1; margin: 0 20px;">
              <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 10px;">
                <div style="flex: 1;">
                  <span class="form-label urdu-text">(اعداد میں) __________</span>
                </div>
                <div style="flex: 1;">
                  <span class="form-label urdu-text">قسم نمبر آرڈر (مندرجہ بالا اور حروف میں)</span>
                </div>
              </div>
              <div style="display: flex; gap: 15px; margin-top: 10px;">
                <div style="flex: 1;">
                  <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                  <span class="form-label urdu-text">رقم نمبر</span>
                </div>
                <div style="flex: 1;">
                  <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                  <span class="form-label urdu-text">بھیجنے والے کے دستخط</span>
                </div>
              </div>
              <div style="display: flex; gap: 15px; margin-top: 10px;">
                <div style="flex: 1;">
                  <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                  <span class="form-label urdu-text">تاریخ</span>
                </div>
                <div style="flex: 1;">
                  <div class="signature-line" style="width: 100%; margin-bottom: 5px;"></div>
                  <span class="form-label urdu-text">وصول نمبر کے واسطے کا نام</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom dashed line section -->
          <div style="border-top: 2px dashed #333; margin-top: 15px; padding-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div style="flex: 1;">
                <div style="display: flex; gap: 10px; align-items: center;">
                  <div class="signature-line" style="width: 150px;"></div>
                  <span class="form-label urdu-text">منی آرڈر</span>
                </div>
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 10px; align-items: center;">
                  <div class="signature-line" style="width: 120px;"></div>
                  <span class="form-label urdu-text">کمیشن واصل کیا</span>
                </div>
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 10px; align-items: center;">
                  <div class="signature-line" style="width: 120px;"></div>
                  <span class="form-label urdu-text">نمبر</span>
                </div>
              </div>
            </div>

            <!-- Bottom boxes and text -->
            <div style="display: flex; align-items: flex-end; margin-top: 15px;">
              <div class="receipt-boxes">
                <div class="receipt-box"></div>
                <div class="receipt-box"></div>
                <div class="receipt-box"></div>
                <div class="receipt-box"></div>
                <div class="receipt-box"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- PAGE 2 - Back Page of Money Order -->
  <div class="a4">
    <div class="mo-form">
      
      <!-- Green Receipt Section -->
      <div class="green-section">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 6px;">Oblong M.O. and Month Stamp of Issue</div>
        <!-- Row 1: long stamp box + narrow box (left), MO no/date (right) -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 8px;">
          <div style="display: flex; align-items: stretch; gap: 6px; width: 58%;">
            <div class="green-box" style="height: 52px; flex: 1;"></div>
            <div class="green-box" style="height: 52px; width: 60px;"></div>
          </div>
          <div style="width: 38%; text-align: right;">
            <div style="font-size: 12px;">M.O. No. <span style="display:inline-block; min-width: 120px; border-bottom: 1px solid #2e7d32;"></span></div>
            <div style="display:flex; justify-content:flex-end; align-items:center; gap: 10px; margin-top: 4px;">
              <span style="font-size: 12px;">Date</span>
              <span style="display:inline-block; min-width: 90px; border-bottom: 1px solid #2e7d32;"></span>
            </div>
          </div>
        </div>

        <!-- Row 2: Issued for (left) + Date and Amount in figures label (right) -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div style="flex: 1; display:flex; align-items:center; gap: 6px; font-size: 12px;">
            <span>Issued for Rs</span>
            <span style="flex: 1; border-bottom: 1px solid #2e7d32;"></span>
          </div>
          <div style="width: 38%; display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
          
            
            <span style="font-size: 12px;">Amount in figures</span>
          </div>
        </div>

        <!-- Row 3: signatures and Rs/Ps container on the same line -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 8px;">
          <div style="width: 43%; text-align: center;">
            <div style="border-top: 1px solid #2e7d32; padding-top: 4px; font-size: 12px;">M.O. Clerk</div>
          </div>
          <div style="width: 43%; text-align: center;">
            <div style="border-top: 1px solid #2e7d32; padding-top: 4px; font-size: 12px;">Issuing Postmaster</div>
          </div>
          <div style="display:flex; justify-content:flex-end;">
            <div style="display:flex; align-items:center; gap: 8px; border: 1px solid #2e7d32; background: #fff; padding: 6px 8px;">
              <span style="font-size: 12px;">Rs.</span>
              <div style="width: 56px; height: 22px; border: 1px solid #2e7d32;"></div>
              <span style="font-size: 12px;">Ps.</span>
              <div style="width: 56px; height: 22px; border: 1px solid #2e7d32;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Middle Section with Instructions -->
      <div style="border-top: 2px dashed #333; padding-top: 10px;">
        <div class="instr-wrap">
          <div class="top-right-box urdu-text"><strong>عام رسید آرڈر</strong><br><span style="font-size: 11px;">(اندراج/افسر)</span></div>
          <div class="center-content">
            <div class="urdu-text" style="font-size: 14px; margin-bottom: 6px; text-align: center;">مندرجہ ذیل خانے ارسال کنندہ کی ہدایات کے مطابق پُر کریں</div>
            <div class="instr-urdu urdu-text">
              دی گئی ہدایات کے مطابق منی آرڈر درست طور پر تحریر کریں<br>
              رقم اور نام واضح لکھیں، غلطی کی صورت میں درست اندراج کروائیں
            </div>
          </div>
        </div>

        <!-- Main Body Section -->
        <div style="display: flex; gap: 15px; align-items: stretch;">
          <!-- Left green writing area (scan-style) -->
          <div class="green-writing" style="flex: 1;">
            <div class="rule"></div>
            <div class="amount">
              <div class="title urdu-text">مبلغ</div>
              <div class="amounts">
                <div class="segment">
                  <span class="urdu-text">روپیہ</span>
                  <div class="hr short"></div>
                </div>
                <div class="segment">
                  <span class="urdu-text">پیسے</span>
                  <div class="hr short"></div>
                </div>
              </div>
            </div>
            <div class="rule"></div>
            <div class="receipt-inline" style="margin-left: 10px;">
              <div class="sq"></div><div class="sq"></div><div class="sq"></div><div class="sq"></div><div class="sq"></div>
              <span class="label urdu-text">(رسید نمبر)</span>
            </div>
            <div class="rule"></div>
            <div class="rule"></div>
            <div class="rule"></div>
            <div class="receipt-inline" style="margin-left: 10px;">
              <div class="sq"></div><div class="sq"></div><div class="sq"></div><div class="sq"></div><div class="sq"></div>
              <span class="label urdu-text">(رسید نمبر)</span>
            </div>
            <div class="rule"></div>
          </div>

          <!-- Right side curly-brace label groups -->
          <div class="back-right">
            <div class="side-group">
              <div class="brace"></div>
              <div class="urdu-text" style="font-size: 12px; text-align: right; line-height: 1.4;">
                رقم بھیجنے والا<br>شناخت/حوالہ
              </div>
            </div>
            <div class="side-group">
              <div class="brace"></div>
              <div class="urdu-text" style="font-size: 12px; text-align: right; line-height: 1.4;">
                وصول کنندہ کا نام<br>اور مکمل پتہ
              </div>
            </div>
            <div class="side-group">
              <div class="brace"></div>
              <div class="urdu-text" style="font-size: 12px; text-align: right; line-height: 1.4;">
                تفصیلات / کمیشن
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Signature Section -->
      <div style="border-top: 2px dashed #333; margin-top: 15px; padding-top: 10px;">
        <div style="text-align: center; margin-bottom: 10px;">
          <span class="urdu-text" style="font-size: 13px;">منی آرڈر بھیجنے والے کے لیے رسید</span>
        </div>

        <div style="display: flex; justify-content: space-between; margin: 15px 0;">
          <div style="flex: 1;">
            <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
            <span class="urdu-text" style="font-size: 11px;">پاکستان پوسٹ</span>
          </div>
          <div style="flex: 1; text-align: center;">
            <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
            <span class="urdu-text" style="font-size: 11px;">منی آرڈر بھیجنے والے کے لیے رسید</span>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin: 15px 0;">
          <div style="flex: 1;">
            <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
            <span class="urdu-text" style="font-size: 11px;">منی آرڈر بھیجنے والے کا نام</span>
          </div>
          <div style="flex: 1;">
            <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
            <span class="urdu-text" style="font-size: 11px;">دستخط</span>
          </div>
        </div>

        <div style="display: flex; gap: 0; margin: 15px 0;">
          <?php for($k = 0; $k < 5; $k++): ?>
          <div style="width: 30px; height: 30px; border: 1px solid #333;"></div>
          <?php endfor; ?>
          <span style="margin-left: 10px; font-size: 11px;" class="urdu-text">(پوسٹ گائیڈ)</span>
        </div>

        <!-- Footer Dashed Line -->
        <div style="border-top: 2px dashed #333; margin-top: 15px; padding-top: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="flex: 1;">
              <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
              <span class="urdu-text" style="font-size: 11px;">پاکستان پوسٹ</span>
            </div>
            <div style="flex: 1; text-align: center;">
              <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
              <span class="urdu-text" style="font-size: 11px;">منی آرڈر بھیجنے والے کے لیے رسید</span>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; margin: 15px 0;">
            <div style="flex: 1;">
              <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
              <span class="urdu-text" style="font-size: 11px;">منی آرڈر بھیجنے والے کا نام</span>
            </div>
            <div style="flex: 1;">
              <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
              <span class="urdu-text" style="font-size: 11px;">دستخط</span>
            </div>
          </div>

          <div style="display: flex; gap: 0; margin: 15px 0;">
            <?php for($k = 0; $k < 5; $k++): ?>
            <div style="width: 30px; height: 30px; border: 1px solid #333;"></div>
            <?php endfor; ?>
            <span style="margin-left: 10px; font-size: 11px;" class="urdu-text">(پوسٹ گائیڈ)</span>
          </div>

          <!-- Footer Dashed Line -->
          <div style="border-top: 2px dashed #333; margin-top: 15px; padding-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div style="flex: 1;">
                <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
                <span class="urdu-text" style="font-size: 11px;">تصدیق</span>
              </div>
              <div style="flex: 1;">
                <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 20px;"></div>
                <span class="urdu-text" style="font-size: 11px;">رویہ</span>
              </div>
              <div style="flex: 1; text-align: center;">
                <span class="urdu-text" style="font-size: 12px;">کیوں منی آرڈر کمیشن رقم آرڈر وصول کرنے والے کے نام<br>جاری کیا گیا استعمال کے لیے پانا ضروری اور بحوالہ تحقیق کے لیے</span>
              </div>
            </div>
          </div>

          <!-- Bottom Footer -->
          <div style="margin-top: 20px; padding-top: 10px; text-align: center;">
            <span style="font-size: 10px; font-style: italic;">Pakistan Post Foundation (Press Division)</span>
            <div style="float: right; font-size: 11px;" class="urdu-text">
              نوٹ: اگر یہ آرڈر کمیشن سے متعلق ہیں تو ان کو پیش کریں
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
