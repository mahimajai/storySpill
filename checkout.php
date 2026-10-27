<?php
require "includes/auth_check.php";

$bookId = (int)($_GET["book_id"] ?? 0);
$mode = $_GET["mode"] ?? "";

$stmt = $pdo->prepare("SELECT b.*, u.name AS uploader_name FROM books b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book || !in_array($mode, ["buy", "rent"], true)) {
    header("Location: index.php");
    exit;
}

$price = (float)($book["price"] ?? 299);
$rentPrice = round($price / 2, 2);

require_once "includes/library_access.php";
if ($mode === "buy" && user_owns_book($pdo, (int)$_SESSION["user_id"], $bookId)) {
    header("Location: read_book.php?id=" . $bookId);
    exit;
}

$payAmount = $mode === "buy" ? $price : $rentPrice;
$err = isset($_GET["err"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $mode === "buy" ? "Buy" : "Rent"; ?> – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .checkout-card {
      max-width: 560px; margin: 36px auto; background: #161616;
      border: 1px solid #333; border-radius: 16px; padding: 28px;
    }
    .checkout-card h2 { font-size: 1.35rem; margin-bottom: 6px; color: #fff; }
    .price-tag { font-size: 1.7rem; color: #c9a84c; font-weight: 700; }
    .pay-methods { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 8px; }
    .pay-method {
      flex: 1; min-width: 120px; text-align: center; padding: 12px 8px;
      border: 2px solid #333; border-radius: 12px; cursor: pointer;
      background: #111; color: #bbb; font-size: 0.82rem; font-weight: 600;
    }
    .pay-method i { display: block; font-size: 1.3rem; margin-bottom: 6px; }
    .pay-method:hover { border-color: #666; color: #fff; }
    .pay-method.active {
      border-color: #c9a84c; color: #c9a84c; background: rgba(201,168,76,0.08);
    }
    .pay-panel {
      display: none; background: #111; border: 1px solid #333;
      border-radius: 12px; padding: 16px; margin-top: 12px;
    }
    .pay-panel.active { display: block; }
    .pay-panel label { font-size: 0.78rem; color: #888; display: block; margin-bottom: 4px; }
    .pay-panel input, .pay-panel select {
      width: 100%; background: #1a1a1a; border: 1px solid #444; color: #eee;
      border-radius: 8px; padding: 10px; margin-bottom: 12px;
    }
    .btn-pay {
      width: 100%; background: #c9a84c; color: #111; border: none;
      border-radius: 50px; padding: 13px; font-weight: 700; margin-top: 8px;
    }
    .btn-pay:hover { background: #e0c060; }
    .note { font-size: 0.72rem; color: #666; margin-top: 12px; }
    .upi-apps { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
    .upi-apps span {
      font-size: 0.72rem; padding: 4px 10px; border-radius: 20px;
      background: #222; color: #aaa; border: 1px solid #333;
    }
    .err { background: rgba(231,76,60,0.15); border: 1px solid #e74c3c; color: #e88; padding: 10px; border-radius: 8px; margin-bottom: 12px; font-size: 0.85rem; }
    .days-wrap { display: flex; align-items: center; gap: 12px; margin: 8px 0 16px; }
    .days-spinner {
      display: inline-flex; flex-direction: column; border: 1px solid #444;
      border-radius: 8px; overflow: hidden; background: #1a1a1a;
    }
    .days-spinner button {
      background: #222; border: none; color: #c9a84c; width: 36px; height: 22px;
      cursor: pointer; line-height: 1; font-size: 0.7rem; padding: 0;
    }
    .days-spinner button:hover { background: #333; }
    .days-spinner input {
      width: 36px; text-align: center; border: none; border-top: 1px solid #333;
      border-bottom: 1px solid #333; background: #111; color: #fff;
      font-weight: 700; font-size: 1rem; padding: 4px 0; margin: 0;
      -moz-appearance: textfield;
    }
    .days-spinner input::-webkit-outer-spin-button,
    .days-spinner input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .days-label { color: #aaa; font-size: 0.9rem; }
  </style>
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div class="app-container">
  <div class="checkout-card">
    <p style="margin:0 0 8px">
      <a href="index.php" style="color:#c9a84c;text-decoration:none">
        <i class="fas fa-arrow-left"></i> Back to shelf
      </a>
    </p>
    <h2><?php echo $mode === "buy" ? "Buy book" : "Rent book"; ?></h2>
    <p style="color:#ccc; margin-bottom:2px"><?php echo htmlspecialchars($book["title"]); ?></p>
    <p style="color:#666; font-size:0.85rem; margin-bottom:12px">
      by <?php echo htmlspecialchars($book["author"]); ?> · <?php echo htmlspecialchars($book["genre"]); ?>
    </p>

    <p class="price-tag">₹<?php echo number_format($payAmount, 2); ?></p>
    <?php if ($mode === "buy"): ?>
      <p style="color:#888; font-size:0.85rem">One-time payment · lifetime PDF access</p>
    <?php else: ?>
      <p style="color:#888; font-size:0.85rem">
        Rent = 50% of buy price (₹<?php echo number_format($price, 2); ?>). Choose 1–7 days. When time ends, rental leaves My Library automatically.
      </p>
    <?php endif; ?>

    <?php if ($err): ?>
      <div class="err">Payment details incomplete. Fill the fields for the selected method.</div>
    <?php endif; ?>

    <form method="POST" action="process_payment.php" id="checkoutForm">
      <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
      <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
      <input type="hidden" name="payment_method" id="payment_method" value="Card">

      <?php if ($mode === "rent"): ?>
        <label style="color:#888;font-size:0.78rem">Rent duration (1–7 days)</label>
        <div class="days-wrap">
          <div class="days-spinner">
            <button type="button" id="daysUp" aria-label="Increase days">&#9650;</button>
            <input type="number" name="days" id="daysInput" value="1" min="1" max="7" required>
            <button type="button" id="daysDown" aria-label="Decrease days">&#9660;</button>
          </div>
          <span class="days-label"><span id="daysText">1</span> day(s)</span>
        </div>
      <?php endif; ?>

      <p style="color:#aaa; font-size:0.85rem; margin:16px 0 6px; font-weight:600">Select payment method</p>
      <div class="pay-methods">
        <div class="pay-method active" data-method="Card"><i class="fas fa-credit-card"></i> Card</div>
        <div class="pay-method" data-method="UPI"><i class="fas fa-qrcode"></i> UPI</div>
        <div class="pay-method" data-method="NetBanking"><i class="fas fa-university"></i> Net Banking</div>
      </div>

      <div class="pay-panel active" id="panel-Card">
        <label>Cardholder name</label>
        <input type="text" name="card_name" id="card_name" placeholder="Name on card"
               value="<?php echo htmlspecialchars($_SESSION["user_name"] ?? ""); ?>">
        <label>Card number</label>
        <input type="text" name="card_number" id="card_number" placeholder="4111 1111 1111 1111" maxlength="19" inputmode="numeric">
        <div class="row g-2">
          <div class="col-6">
            <label>Expiry (MM/YY)</label>
            <input type="text" name="card_exp" id="card_exp" placeholder="08/28" maxlength="5">
          </div>
          <div class="col-6">
            <label>CVV</label>
            <input type="password" name="card_cvv" id="card_cvv" placeholder="123" maxlength="4">
          </div>
        </div>
      </div>

      <div class="pay-panel" id="panel-UPI">
        <div class="upi-apps">
          <span>GPay</span><span>PhonePe</span><span>Paytm</span><span>BHIM</span>
        </div>
        <label>UPI ID</label>
        <input type="text" name="upi_id" id="upi_id" placeholder="yourname@okaxis / @paytm / @ybl">
        <p style="color:#666; font-size:0.72rem; margin:0">Demo: use any ID like <span style="color:#c9a84c">demo@upi</span></p>
      </div>

      <div class="pay-panel" id="panel-NetBanking">
        <label>Select bank</label>
        <select name="bank_name" id="bank_name">
          <option value="">— Choose bank —</option>
          <option value="SBI">State Bank of India</option>
          <option value="HDFC">HDFC Bank</option>
          <option value="ICICI">ICICI Bank</option>
          <option value="Axis">Axis Bank</option>
          <option value="Kotak">Kotak Mahindra</option>
          <option value="PNB">Punjab National Bank</option>
          <option value="Other">Other</option>
        </select>
        <label>Net banking user ID (demo)</label>
        <input type="text" name="nb_user" id="nb_user" placeholder="Bank login ID">
        <label>Password (demo — not stored)</label>
        <input type="password" name="nb_pass" id="nb_pass" placeholder="••••••••">
      </div>

      <button type="submit" class="btn-pay">
        <i class="fas fa-lock"></i> Pay ₹<?php echo number_format($payAmount, 2); ?>
      </button>
      <p class="note">Demo checkout. No real money charged. Payment details are not stored.</p>
    </form>
  </div>
</div>
<script>
(function () {
  const daysInput = document.getElementById('daysInput');
  const daysText = document.getElementById('daysText');
  function clampDays() {
    if (!daysInput) return;
    let v = parseInt(daysInput.value, 10);
    if (isNaN(v) || v < 1) v = 1;
    if (v > 7) v = 7;
    daysInput.value = v;
    if (daysText) daysText.textContent = v;
  }
  if (daysInput) {
    document.getElementById('daysUp').addEventListener('click', () => {
      daysInput.value = Math.min(7, (parseInt(daysInput.value, 10) || 1) + 1);
      clampDays();
    });
    document.getElementById('daysDown').addEventListener('click', () => {
      daysInput.value = Math.max(1, (parseInt(daysInput.value, 10) || 1) - 1);
      clampDays();
    });
    daysInput.addEventListener('change', clampDays);
    daysInput.addEventListener('input', clampDays);
    clampDays();
  }

  const methods = document.querySelectorAll('.pay-method');
  const hiddenMethod = document.getElementById('payment_method');
  const panels = {
    Card: document.getElementById('panel-Card'),
    UPI: document.getElementById('panel-UPI'),
    NetBanking: document.getElementById('panel-NetBanking')
  };
  function setMethod(name) {
    methods.forEach(m => m.classList.toggle('active', m.dataset.method === name));
    Object.keys(panels).forEach(k => panels[k].classList.toggle('active', k === name));
    hiddenMethod.value = name;
    document.getElementById('card_name').required = (name === 'Card');
    document.getElementById('card_number').required = (name === 'Card');
    document.getElementById('card_exp').required = (name === 'Card');
    document.getElementById('card_cvv').required = (name === 'Card');
    document.getElementById('upi_id').required = (name === 'UPI');
    document.getElementById('bank_name').required = (name === 'NetBanking');
    document.getElementById('nb_user').required = (name === 'NetBanking');
    document.getElementById('nb_pass').required = (name === 'NetBanking');
  }
  methods.forEach(m => m.addEventListener('click', () => setMethod(m.dataset.method)));
  setMethod('Card');

  const cardInput = document.getElementById('card_number');
  cardInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 16);
    this.value = v.replace(/(.{4})/g, '$1 ').trim();
  });

  document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    clampDays();
    const method = hiddenMethod.value;
    if (method === 'Card') {
      if ((cardInput.value || '').replace(/\s/g, '').length < 12) {
        e.preventDefault(); alert('Enter a valid card number (demo: 16 digits).');
      }
    } else if (method === 'UPI') {
      if (!document.getElementById('upi_id').value.trim().includes('@')) {
        e.preventDefault(); alert('Enter a valid UPI ID (e.g. name@paytm).');
      }
    } else if (method === 'NetBanking') {
      if (!document.getElementById('bank_name').value || !document.getElementById('nb_user').value.trim()) {
        e.preventDefault(); alert('Select bank and enter net-banking user ID.');
      }
    }
  });
})();
</script>
</body>
</html>