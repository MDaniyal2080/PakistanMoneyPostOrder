<?php
include_once __DIR__ . '/includes/functions.php';

$customers_file = __DIR__ . '/data/customers.json';
$customers = file_exists($customers_file) ? read_json($customers_file) : [];

$contact = $_GET['contact'] ?? '';
$result = ['found' => false];

foreach ($customers as $c) {
    if ($c['contact'] === $contact) {
        $result = [
            'found' => true,
            'name' => $c['name'],
            'address' => $c['address']
        ];
        break;
    }
}

header('Content-Type: application/json');
echo json_encode($result);
