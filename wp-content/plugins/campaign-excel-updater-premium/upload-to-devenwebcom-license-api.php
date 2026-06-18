<?php
header('Content-Type: application/json');

$valid_keys = [
    'ABCDEF12345678' => 'example.com',
    'XYZXYZ98765432' => 'anotherdomain.com',
];

$license_key = $_GET['key'] ?? '';
$domain = $_GET['domain'] ?? '';

if (isset($valid_keys[$license_key]) && $valid_keys[$license_key] === $domain) {
    echo json_encode(['status' => 'valid']);
} else {
    echo json_encode(['status' => 'invalid']);
}
?>
