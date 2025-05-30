
<?php
include 'includes/db.php';
include 'includes/mail.php';

function simulateStatus($card_number) {
    if (empty($card_number)) {
        return 'error';
    }
    
    $first_digit = substr($card_number, 0, 1);
    
    return match($first_digit) {
        '1' => 'approved',
        '2' => 'declined',
        '3' => 'error',
        default => 'approved'
    };
}

// Start transaction for atomic operations
$conn->begin_transaction();

try {
    $status = simulateStatus($_POST['card_number'] ?? '');
    $order_number = uniqid("ORD-");

    // Verify required fields
    $required_fields = ['product_id', 'variant_id', 'quantity', 'total', 'full_name', 'email', 'card_number', 'name_on_card'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Missing required field - $field");
        }
    }

    $product_id = intval($_POST['product_id']);
    $variant_id = intval($_POST['variant_id']);
    $quantity = intval($_POST['quantity']);

    // Check stock availability first
    $stock_result = $conn->query("SELECT stock FROM product_variants WHERE id = $variant_id FOR UPDATE");
    $current_stock = $stock_result->fetch_assoc()['stock'];
    
    if ($current_stock < $quantity) {
        throw new Exception("Not enough stock available for this variant");
    }

    // Get product details
    $product = $conn->query("SELECT p.*, pi.image_url 
                           FROM products p 
                           JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
                           WHERE p.id = $product_id")->fetch_assoc();
    
    if (!$product) {
        throw new Exception("Product not found");
    }

    $variant = $conn->query("SELECT * FROM product_variants WHERE id = $variant_id")->fetch_assoc();
    if (!$variant) {
        throw new Exception("Product variant not found");
    }

    // Create order
    $total = floatval($_POST['total']);
    $conn->query("INSERT INTO orders (order_number, status, total) VALUES ('$order_number', '$status', $total)");
    $order_id = $conn->insert_id;

    // Add order items
    $conn->query("INSERT INTO order_items (order_id, product_id, variant_id, quantity, subtotal)
                 VALUES ($order_id, $product_id, $variant_id, $quantity, $total)");

    // Update inventory (CRITICAL CHANGE)
    $update_result = $conn->query("UPDATE product_variants SET stock = stock - $quantity WHERE id = $variant_id");
    
    if (!$update_result || $conn->affected_rows === 0) {
        throw new Exception("Failed to update inventory");
    }

    // Record customer details
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $city = $conn->real_escape_string($_POST['city'] ?? '');
    $state = $conn->real_escape_string($_POST['state'] ?? '');
    $zip_code = $conn->real_escape_string($_POST['zip_code'] ?? '');
    $card_number = $conn->real_escape_string($_POST['card_number'] ?? '');
    $name_on_card = $conn->real_escape_string($_POST['name_on_card'] ?? '');
    $expiry_date = $conn->real_escape_string($_POST['expiry_date'] ?? '');
    $cvv = $conn->real_escape_string($_POST['cvv'] ?? '');

    $conn->query("INSERT INTO customers (order_id, full_name, email, phone, address, city, state, zip_code, card_number, name_on_card, expiry_date, cvv)
                 VALUES ($order_id, '$full_name', '$email', '$phone', '$address', '$city', '$state', '$zip_code', '$card_number', '$name_on_card', '$expiry_date', '$cvv')");

    // Prepare order details for email
    $order_details = [
        'status' => $status,
        'order_number' => $order_number,
        'full_name' => htmlspecialchars($_POST['full_name']),
        'product_title' => htmlspecialchars($product['title']),
        'product_price' => floatval($product['price']),
        'product_image' => htmlspecialchars($product['image_url']),
        'variant_color' => htmlspecialchars($variant['color']),
        'variant_size' => htmlspecialchars($variant['size']),
        'quantity' => $quantity,
        'total' => floatval($_POST['total']),
        'address' => isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '',
        'city' => isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '',
        'state' => isset($_POST['state']) ? htmlspecialchars($_POST['state']) : '',
        'zip_code' => isset($_POST['zip_code']) ? htmlspecialchars($_POST['zip_code']) : '',
        'simulation_input' => substr($_POST['card_number'], 0, 1),
        'remaining_stock' => ($current_stock - $quantity) // Added stock information
    ];

    // Commit transaction if everything succeeded
    $conn->commit();

    // Send email
    $subject = match($status) {
        'approved' => "✅ Order Confirmed: $order_number",
        'declined' => "❌ Payment Declined: $order_number",
        default => "⚠️ Order Processing Error: $order_number"
    };

    sendMail($_POST['email'], $subject, '', $order_details);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    die("Error processing order: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Thank You</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-xl mx-auto bg-white rounded p-6 text-center shadow">
        <h2 class="text-2xl font-bold mb-4">
            <?= match ($status) {
                'approved' => '✅ Order Confirmed!',
                'declined' => '❌ Payment Declined',
                default => '⚠️ Order Processing Error'
            } ?>
        </h2>
        <p>Order ID: <strong><?= htmlspecialchars($order_number) ?></strong></p>

        <?php if ($status === 'declined' || $status === 'error'): ?>
            <div class="bg-red-100 border-l-4 border-red-500 p-4 my-4 text-left">
                <p class="font-bold">Simulation Input:</p>
                <p>You entered: <strong><?= htmlspecialchars($order_details['simulation_input']) ?></strong></p>
                <p class="mt-2">
                    <?= match ($order_details['simulation_input']) {
                        '2' => 'This simulates a declined payment (e.g., insufficient funds).',
                        '3' => 'This simulates a payment gateway failure.',
                        default => 'Unexpected simulation input.'
                    } ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-6 p-4 bg-gray-50 rounded text-left">
            <h3 class="font-bold mb-2">Order Summary:</h3>
            <div class="flex items-center">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" class="w-20 h-20 object-contain mr-4">
                <div>
                    <p><?= htmlspecialchars($product['title']) ?></p>
                    <p><?= htmlspecialchars($variant['color']) ?> / <?= htmlspecialchars($variant['size']) ?></p>
                    <p>Quantity: <?= intval($_POST['quantity']) ?></p>
                    <p class="font-bold">$<?= number_format($_POST['total'], 2) ?></p>
                </div>
            </div>
        </div>
        <p class="mt-4">
            <?= match ($status) {
                'approved' => 'Thank you for your order! A confirmation has been sent to your email.',
                'declined' => 'We\'re sorry, but your payment was declined. Please check your payment details and try again.',
                default => 'We encountered an error processing your order. Our team has been notified and will contact you shortly.'
            } ?>
        </p>
    </div>
</body>

</html>