<?php
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['variant_id'])) {
    die(json_encode(['error' => 'Variant ID required']));
}

$variant_id = intval($_GET['variant_id']);
$result = $conn->query("SELECT stock FROM product_variants WHERE id = $variant_id");

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Variant not found']);
}
?>