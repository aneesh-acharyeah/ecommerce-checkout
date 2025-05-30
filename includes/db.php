<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ecommerce';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}



// For encryption/decryption
define('ENCRYPTION_KEY', '9784123668'); // Change to something secure
define('ENCRYPTION_IV', '1234567891011121');     // Must be 16 characters

function encryptId($id)
{
    return urlencode(base64_encode(openssl_encrypt($id, 'AES-128-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV)));
}

function decryptId($encrypted)
{
    $decoded = base64_decode(urldecode($encrypted));
    return openssl_decrypt($decoded, 'AES-128-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}



?>