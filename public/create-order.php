<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/functions.php';

$products = read_json(__DIR__ . '/../data/products.json');
$orders_file = __DIR__ . '/../data/orders.json';
$orders = read_json($orders_file);
$customers_file = __DIR__ . '/../data/customers.json';
$customers = file_exists($customers_file) ? read_json($customers_file) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Find selected product
    $selected = null;
    foreach ($products as $p) {
        if ($p['id'] == $_POST['product']) $selected = $p;
    }

    $total = $_POST['unit_price'] * $_POST['qty'];

    // Save order
    $orders[] = [
        'id' => time(),
        'order_no' => $_POST['order_no'] ?: ('ORD-' . time()),
        'product_id' => $_POST['product'],
        'qty' => (int)$_POST['qty'],
        'weight' => $selected['weight'],
        'unit_price' => $_POST['unit_price'],
        'total' => $total,
        'customer' => [
            'contact' => $_POST['contact'],
            'name' => $_POST['customer_name'],
            'address' => $_POST['address']
        ],
        'remarks' => $_POST['remarks'],
        'status' => 'Pending',
        'date' => date('Y-m-d')
    ];
    write_json($orders_file, $orders);

    // Save or update customer
    $exists = false;
    foreach ($customers as &$c) {
        if ($c['contact'] === $_POST['contact']) {
            $c['name'] = $_POST['customer_name'];
            $c['address'] = $_POST['address'];
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $customers[] = [
            'contact' => $_POST['contact'],
            'name' => $_POST['customer_name'],
            'address' => $_POST['address']
        ];
    }
    write_json($customers_file, $customers);

    header("Location: reports.php");
    exit;
}
?>

<div class="min-h-screen bg-gray-100 py-10 px-4">
  <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-md">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Create New Order</h1>

    <form method="POST" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Select Product -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Select Product</label>
          <select name="product" id="product" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" required onchange="fillDetails()">
            <option value="">Choose a product</option>
            <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>" data-weight="<?= $p['weight'] ?>" data-price="<?= $p['unit_price'] ?>">
              <?= htmlspecialchars($p['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Quantity -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Quantity</label>
          <input type="number" name="qty" id="qty" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" placeholder="Enter quantity" min="1" required oninput="calcTotal()">
        </div>

        <!-- Weight -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Weight (kg)</label>
          <input type="text" id="weight" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg p-3" placeholder="Auto-filled">
        </div>

        <!-- Unit Price -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Unit Price (PKR)</label>
          <input type="number" step="0.01" name="unit_price" id="unit_price"
            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500"
            placeholder="Auto-filled or edit manually" oninput="calcTotal()">
        </div>

        <!-- Customer Contact -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Customer Contact</label>
          <input type="text" name="contact" id="contact" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500"
            placeholder="Enter contact number" required onblur="fetchCustomer()">
        </div>

        <!-- Customer Name -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Customer Name</label>
          <input type="text" name="customer_name" id="customer_name" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" placeholder="Enter customer name">
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label class="block mb-2 font-semibold text-gray-700">Customer Address</label>
          <textarea name="address" id="address" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" placeholder="Enter customer address"></textarea>
        </div>

        <!-- Order No -->
        <div>
          <label class="block mb-2 font-semibold text-gray-700">Order Number</label>
          <input type="text" name="order_no" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" placeholder="Auto-generated if empty">
        </div>

        <!-- Remarks -->
        <div class="md:col-span-2">
          <label class="block mb-2 font-semibold text-gray-700">Remarks</label>
          <textarea name="remarks" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500" placeholder="Additional notes"></textarea>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-between items-center border-t pt-4">
        <div class="text-lg font-semibold text-gray-700">
          Total Amount: <span id="total" class="text-blue-600 font-bold">0</span> PKR
        </div>
        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
          Save Order
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function fillDetails(){
  const product = document.querySelector('#product');
  const opt = product.options[product.selectedIndex];
  document.querySelector('#weight').value = opt.dataset.weight || '';
  document.querySelector('#unit_price').value = opt.dataset.price || '';
  calcTotal();
}

function calcTotal(){
  const price = parseFloat(document.querySelector('#unit_price').value || 0);
  const qty = parseInt(document.querySelector('#qty').value || 0);
  document.querySelector('#total').textContent = (price * qty).toLocaleString();
}

// Fetch customer details if contact exists
function fetchCustomer(){
  const contact = document.querySelector('#contact').value.trim();
  if (!contact) return;
  fetch(`../fetch_customer.php?contact=${contact}`)
    .then(res => res.json())
    .then(data => {
      if (data.found) {
        document.querySelector('#customer_name').value = data.name;
        document.querySelector('#address').value = data.address;
      } else {
        document.querySelector('#customer_name').value = '';
        document.querySelector('#address').value = '';
      }
    });
}
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
