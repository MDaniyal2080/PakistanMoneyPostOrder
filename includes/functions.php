<?php
// includes/functions.php

function read_json($file) {
    if (!file_exists($file)) return [];
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function write_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// --- USER LOGIN ---
function verify_user($username, $password) {
    $users = read_json(__DIR__ . '/../data/users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return true;
        }
    }
    return false;
}

// --- FETCH COUNTS ---
function get_summary() {
    $products = read_json(__DIR__ . '/../data/products.json');
    $orders   = read_json(__DIR__ . '/../data/orders.json');

    $total_products = count($products);
    $total_orders   = count($orders);
    $pending_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'pending'));
    $total_revenue  = array_sum(array_map(fn($o) => $o['total'], $orders));

    return [
        'products' => $total_products,
        'orders'   => $total_orders,
        'pending'  => $pending_orders,
        'revenue'  => $total_revenue
    ];
}
?>
