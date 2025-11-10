<?php
include_once __DIR__ . '/../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['id']) || empty($data['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$ordersFile = __DIR__ . '/../data/orders.json';
$orders = read_json($ordersFile);

foreach ($orders as &$order) {
    if ($order['id'] == $data['id']) {
        $order['status'] = $data['status'];
        break;
    }
}

write_json($ordersFile, $orders);
echo json_encode(['success' => true]);
?>
