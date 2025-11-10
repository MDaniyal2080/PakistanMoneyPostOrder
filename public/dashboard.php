<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/functions.php';

$orders = read_json(__DIR__ . '/../data/orders.json');
$products = read_json(__DIR__ . '/../data/products.json');
$customers = read_json(__DIR__ . '/../data/customers.json');

// Calculate summary
$total_products = count($products);
$total_orders = count($orders);
$total_customers = count($customers);

$pending_orders = 0;
$delivered_orders = 0;
$cancelled_orders = 0;
$total_revenue = 0;

foreach ($orders as $order) {
    $status = strtolower($order['status']);
    if ($status === 'pending') $pending_orders++;
    elseif ($status === 'completed' || $status === 'delivered') {
        $delivered_orders++;
        $total_revenue += $order['total'];
    }
    elseif ($status === 'cancelled') $cancelled_orders++;
}

// Recent orders with product details
$recent_orders = array_slice(array_reverse($orders), 0, 5);
$recent_orders_with_details = [];

foreach ($recent_orders as $order) {
    $product_name = '';
    $product_category = '';
    foreach ($products as $p) {
        if ($p['id'] == $order['product_id']) {
            $product_name = $p['name'];
            $product_category = $p['category'] ?? 'General';
            break;
        }
    }
    
    $recent_orders_with_details[] = [
        'order_no' => $order['order_no'],
        'product_name' => $product_name,
        'product_category' => $product_category,
        'qty' => $order['qty'],
        'customer_name' => $order['customer']['name'] ?? 'Unknown',
        'customer_email' => $order['customer']['email'] ?? '',
        'total' => $order['total'],
        'status' => $order['status'],
        'date' => $order['date']
    ];
}

// Calculate performance metrics
$completion_rate = $total_orders > 0 ? round(($delivered_orders / $total_orders) * 100, 1) : 0;
$average_order_value = $delivered_orders > 0 ? round($total_revenue / $delivered_orders, 2) : 0;
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 lg:p-6">
    <!-- Dashboard Header -->
    <div class="mb-8 lg:mb-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-600 mt-2 lg:mt-1 text-sm lg:text-base">Comprehensive view of your store performance and analytics</p>
            </div>
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Live
                </span>
                <span class="text-sm text-gray-500"><?= date('F j, Y') ?></span>
            </div>
        </div>
    </div>

    

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Total Products -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Total Products</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $total_products ?></p>
                    <p class="text-blue-100 text-xs lg:text-sm mt-1">Available in store</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Total Orders</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $total_orders ?></p>
                    <p class="text-purple-100 text-xs lg:text-sm mt-1">All time orders</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Total Customers</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $total_customers ?></p>
                    <p class="text-green-100 text-xs lg:text-sm mt-1">Registered customers</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Pending Orders</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $pending_orders ?></p>
                    <p class="text-yellow-100 text-xs lg:text-sm mt-1">Awaiting processing</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Delivered Orders</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $delivered_orders ?></p>
                    <p class="text-green-100 text-xs lg:text-sm mt-1">Successfully completed</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-4 lg:p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm lg:text-base font-medium opacity-90">Cancelled Orders</p>
                    <p class="text-3xl lg:text-4xl font-bold mt-2"><?= $cancelled_orders ?></p>
                    <p class="text-red-100 text-xs lg:text-sm mt-1">Cancelled or refunded</p>
                </div>
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 lg:py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Recent Orders</h2>
                <a href="reports.php" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200 flex items-center">
                    View All Orders
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <?php if (empty($recent_orders_with_details)): ?>
            <div class="px-4 lg:px-6 py-8 lg:py-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg font-medium">No recent orders found</p>
                <p class="text-gray-400 text-sm mt-1">New orders will appear here once they are placed</p>
            </div>
        <?php else: ?>
            <!-- Mobile View - Cards -->
            <div class="lg:hidden p-4 space-y-4">
                <?php foreach ($recent_orders_with_details as $order): ?>
                    <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg"><?= htmlspecialchars($order['order_no']) ?></h3>
                                <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($order['product_name']) ?></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?= strtolower($order['status']) === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                   (strtolower($order['status']) === 'completed' || strtolower($order['status']) === 'delivered' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200') ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">Customer</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Quantity</p>
                                <p class="font-medium text-gray-900"><?= $order['qty'] ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Total</p>
                                <p class="font-medium text-gray-900">PKR <?= number_format($order['total']) ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Date</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($order['date']) ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-500">Category: <?= htmlspecialchars($order['product_category']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop View - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recent_orders_with_details as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($order['order_no']) ?></span>
                                        <p class="text-xs text-gray-500 mt-1">Qty: <?= $order['qty'] ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['product_name']) ?></span>
                                        <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($order['product_category']) ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></span>
                                        <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($order['customer_email']) ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">PKR <?= number_format($order['total']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        <?= strtolower($order['status']) === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           (strtolower($order['status']) === 'completed' || strtolower($order['status']) === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($order['date']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Stats Footer -->
    <div class="mt-6 lg:mt-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?= $total_products ?></p>
            <p class="text-xs text-gray-500 mt-1">Products</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?= $total_orders ?></p>
            <p class="text-xs text-gray-500 mt-1">Orders</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?= $total_customers ?></p>
            <p class="text-xs text-gray-500 mt-1">Customers</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">PKR <?= number_format($total_revenue) ?></p>
            <p class="text-xs text-gray-500 mt-1">Revenue</p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>