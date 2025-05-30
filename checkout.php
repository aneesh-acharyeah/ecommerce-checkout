<?php include 'includes/db.php';

$product_id = intval(decryptId($_GET['product_id']));
$variant_id = intval(decryptId($_GET['variant_id']));
$quantity = intval($_GET['quantity']);

$product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
$variant = $conn->query("SELECT * FROM product_variants WHERE id = $variant_id")->fetch_assoc();
$total = $product['price'] * $quantity;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            border: 1px solid #d1d5db;
            padding: 0.5rem;
            width: 100%;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: -0.75rem;
            margin-bottom: 1rem;
        }

        .error-input {
            border-color: #ef4444;
        }
    </style>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Checkout</h2>
        <div class="flex items-start mb-6">
            <?php
            $mainImage = $conn->query("SELECT image_url FROM product_images WHERE product_id = $product_id AND is_primary = 1")->fetch_assoc();
            ?>
            <img src="<?= $mainImage['image_url'] ?>" class="w-32 h-32 object-contain mr-4 border rounded">
            <div>
                <p class="font-bold"><?= $product['title'] ?></p>
                <p>Variant: <?= $variant['color'] ?> / <?= $variant['size'] ?></p>
                <p>Quantity: <?= $quantity ?></p>
                <p class="text-lg font-bold">Total: $<?= number_format($total, 2) ?></p>
            </div>
        </div>

        <form id="checkoutForm" action="thankyou.php" method="POST" class="mt-6">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <input type="hidden" name="variant_id" value="<?= $variant_id ?>">
            <input type="hidden" name="quantity" value="<?= $quantity ?>">
            <input type="hidden" name="total" value="<?= $total ?>">

            <!-- Personal Information -->
            <h3 class="text-lg font-semibold mb-3 text-gray-700">Personal Information</h3>

            <label for="full_name" class="form-label">Full Name</label>
            <input id="full_name" name="full_name" placeholder="John Doe" class="form-input" required>
            <div id="full_name_error" class="error-message"></div>

            <label for="email" class="form-label">Email Address</label>
            <input id="email" name="email" type="email" placeholder="john@example.com" class="form-input" required>
            <div id="email_error" class="error-message"></div>

            <label for="phone" class="form-label">Phone Number</label>
            <input id="phone" name="phone" placeholder="+1 (555) 123-4567" class="form-input" required>
            <div id="phone_error" class="error-message"></div>

            <!-- Shipping Address -->
            <h3 class="text-lg font-semibold mb-3 text-gray-700 mt-6">Shipping Address</h3>

            <label for="address" class="form-label">Street Address</label>
            <input id="address" name="address" placeholder="123 Main St" class="form-input" required>
            <div id="address_error" class="error-message"></div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="city" class="form-label">City</label>
                    <input id="city" name="city" placeholder="New York" class="form-input" required>
                    <div id="city_error" class="error-message"></div>
                </div>
                <div>
                    <label for="state" class="form-label">State</label>
                    <input id="state" name="state" placeholder="NY" class="form-input" required>
                    <div id="state_error" class="error-message"></div>
                </div>
                <div>
                    <label for="zip_code" class="form-label">ZIP Code</label>
                    <input id="zip_code" name="zip_code" placeholder="10001" class="form-input" required>
                    <div id="zip_code_error" class="error-message"></div>
                </div>
            </div>

            <!-- Payment Information -->
            <h3 class="text-lg font-semibold mb-3 text-gray-700 mt-6">Payment Information</h3>

            <label for="name_on_card" class="form-label">Name on Card</label>
            <input id="name_on_card" name="name_on_card" placeholder="JOHN DOE" class="form-input" required>
            <div id="name_on_card_error" class="error-message"></div>

            <label for="card_number" class="form-label">Card Number (Enter 1, 2, or 3 to simulate)</label>
            <input id="card_number" name="card_number" maxlength="1" placeholder="1 / 2 / 3" class="form-input"
                required>
            <div id="card_number_error" class="error-message"></div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiry_date" class="form-label">Expiration Date</label>
                    <input id="expiry_date" name="expiry_date" type="month" class="form-input" required>
                    <div id="expiry_date_error" class="error-message"></div>
                </div>
                <div>
                    <label for="cvv" class="form-label">CVV</label>
                    <input id="cvv" name="cvv" maxlength="3" placeholder="123" class="form-input" required>
                    <div id="cvv_error" class="error-message"></div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full mt-6">
                Place Order
            </button>
        </form>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            let isValid = true;

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error-input'));

            // Full Name validation
            const fullName = document.getElementById('full_name').value.trim();
            if (!fullName) {
                showError('full_name', 'Full name is required');
                isValid = false;
            }

            // Email validation
            const email = document.getElementById('email').value.trim();
            if (!email) {
                showError('email', 'Email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Phone validation
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                showError('phone', 'Phone number is required');
                isValid = false;
            } else if (!/^[\d\s\+\-\(\)]{10,}$/.test(phone)) {
                showError('phone', 'Please enter a valid phone number');
                isValid = false;
            }

            // Address validation
            const address = document.getElementById('address').value.trim();
            if (!address) {
                showError('address', 'Address is required');
                isValid = false;
            }

            // City validation
            const city = document.getElementById('city').value.trim();
            if (!city) {
                showError('city', 'City is required');
                isValid = false;
            }

            // State validation
            const state = document.getElementById('state').value.trim();
            if (!state) {
                showError('state', 'State is required');
                isValid = false;
            }

            // ZIP Code validation
            const zipCode = document.getElementById('zip_code').value.trim();
            if (!zipCode) {
                showError('zip_code', 'ZIP code is required');
                isValid = false;
            } else if (!/^\d{5}(-\d{4})?$/.test(zipCode)) {
                showError('zip_code', 'Please enter a valid ZIP code');
                isValid = false;
            }

            // Name on Card validation
            const nameOnCard = document.getElementById('name_on_card').value.trim();
            if (!nameOnCard) {
                showError('name_on_card', 'Name on card is required');
                isValid = false;
            } else if (!/^[a-zA-Z\s]+$/.test(nameOnCard)) {
                showError('name_on_card', 'Please enter a valid name (letters only)');
                isValid = false;
            }

            // Card Number validation (simplified for simulation)
            const cardNumber = document.getElementById('card_number').value.trim();
            if (!cardNumber) {
                showError('card_number', 'Please enter 1, 2, or 3 to simulate payment');
                isValid = false;
            } else if (!/^[123]$/.test(cardNumber)) {
                showError('card_number', 'Please enter only 1, 2, or 3 to simulate payment');
                isValid = false;
            }

            // Expiration Date validation
            const expiryDate = document.getElementById('expiry_date').value;
            if (!expiryDate) {
                showError('expiry_date', 'Expiration date is required');
                isValid = false;
            } else {
                const [year, month] = expiryDate.split('-');
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1;

                if (year < currentYear || (year == currentYear && month < currentMonth)) {
                    showError('expiry_date', 'Card has expired');
                    isValid = false;
                }
            }

            // CVV validation
            const cvv = document.getElementById('cvv').value.trim();
            if (!cvv) {
                showError('cvv', 'CVV is required');
                isValid = false;
            } else if (!/^\d{3}$/.test(cvv)) {
                showError('cvv', 'CVV must be 3 digits');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });


        const variantId = <?= $variant_id ?>;
        const quantity = parseInt(document.getElementById('quantity').value);

        try {
            const response = await fetch(`check_stock.php?variant_id=${variantId}`);
            const data = await response.json();

            if (data.stock < quantity) {
                showError('quantity', `Only ${data.stock} items available in stock`);
                e.preventDefault();
                return;
            }
        } catch (error) {
            console.error('Error checking stock:', error);
            // Continue with submission if stock check fails
        }


        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(`${fieldId}_error`);

            field.classList.add('error-input');
            errorElement.textContent = message;
        }

        // Restrict card number input to only 1, 2, or 3
        document.getElementById('card_number').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^123]/g, '');
            if (this.value.length > 1) {
                this.value = this.value.slice(0, 1);
            }
        });

        // Restrict CVV input to only numbers and max 3 digits
        document.getElementById('cvv').addEventListener('input', function (e) {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 3) {
                this.value = this.value.slice(0, 3);
            }
        });

        // Restrict phone input to numbers, spaces, +, -, (, )
        document.getElementById('phone').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9\s\+\-\(\)]/g, '');
        });

        // Format name on card to uppercase
        document.getElementById('name_on_card').addEventListener('input', function (e) {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>

</html>