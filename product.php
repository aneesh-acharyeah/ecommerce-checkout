<?php include 'includes/db.php';
$id = intval(decryptId($_GET['id']));
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
$images = $conn->query("SELECT * FROM product_images WHERE product_id = $id");
$variants = $conn->query("SELECT * FROM product_variants WHERE product_id = $id");
$related = $conn->query("SELECT p.*, pi.image_url FROM products p JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 WHERE p.id != $id LIMIT 3");
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $product['title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white rounded p-6 shadow">
        <div class="grid md:grid-cols-2 gap-4">
            <!-- Add this in the product.php where images are displayed -->
            <div class="mb-4">
                <!-- Main Image -->
                <img src="<?= $images->fetch_assoc()['image_url'] ?>" id="mainProductImage"
                    class="w-full h-96 object-contain mb-4 border rounded">

                <!-- Thumbnail Gallery -->
                <div class="flex space-x-2 overflow-x-auto py-2">
                    <?php
                    $images->data_seek(0); // Reset pointer
                    while ($img = $images->fetch_assoc()):
                        ?>
                        <img src="<?= $img['image_url'] ?>"
                            class="thumbnail w-20 h-20 object-cover cursor-pointer border rounded hover:border-blue-500"
                            onclick="document.getElementById('mainProductImage').src = this.src">
                    <?php endwhile; ?>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2"><?= $product['title'] ?></h2>
                <p class="mb-4"><?= $product['description'] ?></p>
                <p class="text-xl font-semibold mb-4">$<?= $product['price'] ?></p>
                <form action="checkout.php" method="GET">
                    <input type="hidden" name="product_id" value="<?= encryptId($product['id']) ?>">
                    <label>Variant:</label>
                    <select name="variant_id" class="border p-2 w-full mb-4" required>
                        <?php foreach ($variants as $v): ?>
                            <option value="<?= encryptId($v['id']) ?>"><?= $v['color'] ?> / <?= $v['size'] ?> (Stock:
                                <?= $v['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Quantity:</label>
                    <input type="number" name="quantity" min="1" value="1" class="border p-2 w-full mb-4" required>
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add to Cart</button>
                </form>
            </div>
        </div>
        <h3 class="text-xl font-bold mt-8 mb-4">Related Products</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($related as $r): ?>
                <div class="bg-gray-100 p-3 rounded">
                    <img src="<?= $r['image_url'] ?>" class="h-32 object-cover w-full rounded mb-2">
                    <h4 class="font-semibold"><?= $r['title'] ?></h4>
                    <p>$<?= $r['price'] ?></p>
                    <a href="product.php?id=<?= encryptId($r['id']) ?>" class="text-blue-600">View</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="js/main.js"></script>

</body>

</html>