<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/functions.php';

$orders = read_json(__DIR__ . '/../data/orders.json');
$products = read_json(__DIR__ . '/../data/products.json');

function product_name($id, $products) {
    foreach ($products as $p) {
        if ($p['id'] == $id) return $p['name'];
    }
    return 'Unknown Product';
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 sm:mb-0">All Orders</h1>
        
        <!-- Search and Filter Controls -->
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative">
                <input type="text" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <form id="ordersForm" method="post">
        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3">
                            <input type="checkbox" onclick="toggleAll(this)" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (PKR)</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 pl-6 pr-3">
                                    <input type="checkbox" class="order-check h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($o['order_no']) ?></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars(product_name($o['product_id'], $products)) ?></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($o['customer']['name']) ?></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= number_format($o['total']) ?></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span 
                                        class="status-badge inline-flex px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors duration-200
                                            <?= strtolower($o['status']) === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                (strtolower($o['status']) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') ?>"
                                        data-id="<?= $o['id'] ?>"
                                        data-status="<?= htmlspecialchars($o['status']) ?>"
                                    >
                                        <?= htmlspecialchars($o['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <?= htmlspecialchars($o['date']) ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="invoice.php?id=<?= $o['id'] ?>" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $o): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="order-check h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                <div>
                                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($o['order_no']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($o['date']) ?></p>
                                </div>
                            </div>
                            <span 
                                class="status-badge inline-flex px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors duration-200
                                    <?= strtolower($o['status']) === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        (strtolower($o['status']) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') ?>"
                                data-id="<?= $o['id'] ?>"
                                data-status="<?= htmlspecialchars($o['status']) ?>"
                            >
                                <?= htmlspecialchars($o['status']) ?>
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500">Product</p>
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars(product_name($o['product_id'], $products)) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Customer</p>
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($o['customer']['name']) ?></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <div>
                                <p class="text-xs text-gray-500">Amount</p>
                                <p class="text-sm font-medium text-gray-900">PKR <?= number_format($o['total']) ?></p>
                            </div>
                            <a href="invoice.php?id=<?= $o['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                View Invoice
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="hidden mt-6 flex flex-wrap gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <span class="text-sm text-gray-700 mr-2">Actions for selected orders:</span>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download Label
            </button>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download Money Order
            </button>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download Money Order (Back)
            </button>
        </div>
    </form>
</div>

<!-- Status Popup -->
<div id="statusPopup" class="hidden absolute z-10 bg-white rounded-lg shadow-lg border border-gray-200 text-sm overflow-hidden">
    <div class="status-option px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150" data-value="Pending">
        <span class="inline-block w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>
        Pending
    </div>
    <div class="status-option px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150" data-value="Completed">
        <span class="inline-block w-3 h-3 rounded-full bg-green-400 mr-2"></span>
        Delivered
    </div>
    <div class="status-option px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150" data-value="Cancelled">
        <span class="inline-block w-3 h-3 rounded-full bg-gray-400 mr-2"></span>
        Cancelled
    </div>
</div>

<script>
function toggleAll(main) {
    document.querySelectorAll('.order-check').forEach(c => c.checked = main.checked);
    toggleActions();
}

document.querySelectorAll('.order-check').forEach(c => c.addEventListener('change', toggleActions));

function toggleActions() {
    const checked = [...document.querySelectorAll('.order-check')].some(c => c.checked);
    document.querySelector('#bulkActions').classList.toggle('hidden', !checked);
}

// Handle status click
const popup = document.getElementById('statusPopup');
let currentBadge = null;

document.querySelectorAll('.status-badge').forEach(badge => {
    badge.addEventListener('click', e => {
        currentBadge = badge;
        const rect = badge.getBoundingClientRect();
        popup.style.top = `${rect.bottom + window.scrollY + 4}px`;
        popup.style.left = `${rect.left + window.scrollX}px`;
        popup.classList.remove('hidden');
        
        // Position adjustment for mobile
        if (window.innerWidth < 768) {
            const popupWidth = popup.offsetWidth;
            if (rect.left + popupWidth > window.innerWidth) {
                popup.style.left = `${window.innerWidth - popupWidth - 10}px`;
            }
        }
    });
});

document.querySelectorAll('.status-option').forEach(option => {
    option.addEventListener('click', async e => {
        const newStatus = e.target.dataset.value;
        const orderId = currentBadge.dataset.id;

        // Update UI instantly
        currentBadge.textContent = newStatus;
        currentBadge.dataset.status = newStatus;
        currentBadge.className = 'status-badge inline-flex px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors duration-200';
        
        if (newStatus === 'Pending') {
            currentBadge.classList.add('bg-yellow-100', 'text-yellow-800');
        } else if (newStatus === 'Completed') {
            currentBadge.classList.add('bg-green-100', 'text-green-800');
        } else {
            currentBadge.classList.add('bg-gray-100', 'text-gray-800');
        }

        popup.classList.add('hidden');

        // Send update request
        try {
            await fetch('update_status.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: orderId, status: newStatus })
            });
        } catch (error) {
            console.error('Failed to update status:', error);
            // Optionally revert the UI change on error
        }
    });
});

// Hide popup when clicking elsewhere
document.addEventListener('click', e => {
    if (!popup.contains(e.target) && !e.target.classList.contains('status-badge')) {
        popup.classList.add('hidden');
    }
});

// Search functionality (placeholder)
document.querySelector('input[placeholder="Search orders..."]').addEventListener('input', function(e) {
    // Implement search functionality here
    console.log('Search:', e.target.value);
});

// Filter functionality (placeholder)
document.querySelector('select').addEventListener('change', function(e) {
    // Implement filter functionality here
    console.log('Filter:', e.target.value);
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>