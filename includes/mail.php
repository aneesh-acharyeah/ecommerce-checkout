<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

function sendMail($to, $subject, $body, $order_details = [])
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = 'USERNAME';
        $mail->Password = 'PASSWORD';
        $mail->Port = 2525;
        // $mail->SMTPDebug = 2;

        $mail->setFrom('noreply@store.com', 'eCommerce Store');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Enhanced email template
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9fafb; }
                .order-item { display: flex; margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; }
                .order-image { width: 80px; height: 80px; object-fit: contain; margin-right: 15px; }
                .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . ($order_details['status'] === 'approved' ? 'Order Confirmed!' :
            ($order_details['status'] === 'declined' ? 'Payment Declined' : 'Order Processing Error')) . '</h1>
                </div>
                <div class="content">
                    <p>Dear ' . $order_details['full_name'] . ',</p>
                    
                    ' . ($order_details['status'] === 'approved' ?
            '<p>Thank you for your order! Your payment has been approved and your order is being processed.</p>' :
            ($order_details['status'] === 'declined' ?
                '<p>We\'re sorry, but your payment was declined. Please check your payment details and try again.</p>' :
                '<p>We encountered an error processing your order. Our team has been notified and will contact you shortly.</p>')) . '
                    
                    <h3>Order Details</h3>
                    <p>Order Number: <strong>' . $order_details['order_number'] . '</strong></p>
                    
                    <div class="order-item">
                        <img src="' . $order_details['product_image'] . '" class="order-image">
                        <div>
                            <h4>' . $order_details['product_title'] . '</h4>
                            <p>Variant: ' . $order_details['variant_color'] . ' / ' . $order_details['variant_size'] . '</p>
                            <p>Quantity: ' . $order_details['quantity'] . '</p>
                            <p>Price: $' . number_format($order_details['product_price'], 2) . '</p>
                        </div>
                    </div>
                    
                    <p><strong>Total Amount: $' . number_format($order_details['total'], 2) . '</strong></p>
                    
                    ' . ($order_details['status'] === 'approved' ?
            '<p>Your order will be shipped to:</p>
                        <p>' . $order_details['address'] . '<br>
                        ' . $order_details['city'] . ', ' . $order_details['state'] . ' ' . $order_details['zip_code'] . '</p>' : '') . '
                </div>
                <div class="footer">
                    <p>If you have any questions, please contact our support team.</p>
                    <p>© ' . date('Y') . ' Your Store Name. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}
?>