<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .product-card {
            transition: transform 0.3s ease;
            height: 420px;
            /* Increased height */
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 280px;
            /* Better image height */
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-gray-100 p-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $result = $conn->query("SELECT p.*, pi.image_url FROM products p JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1");
        while ($row = $result->fetch_assoc()):
            ?>
            <a href="product.php?id=<?= encryptId($row['id']) ?>" class="product-card bg-white rounded shadow p-4 block">
                <img src="<?= $row['image_url'] ?>" class="w-full product-image rounded mb-4">
                <h2 class="text-lg font-bold"><?= $row['title'] ?></h2>
                <p class="text-gray-600">$<?= number_format($row['price'], 2) ?></p>
            </a>
        <?php endwhile; ?>
    </div>
    <script src="js/main.js"></script>
</body>

</html>