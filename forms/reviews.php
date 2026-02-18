<?php
/**
 * CarGent Mobile - Customer Review Controller
 * Handles star ratings and feedback via Mailtrap
 */

// 1. Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- MAILTRAP CREDENTIALS (Match your contact.php) ---
    $host     = 'sandbox.smtp.mailtrap.io';
    $port     = '587';
    $username = 'f38b98a1ba31c1'; // Your username
    $password = 'ae3323d6ee7eb7'; // Click the eye icon in Mailtrap to get this

    ini_set("SMTP", $host);
    ini_set("smtp_port", $port);
    ini_set("sendmail_from", "support@cargent.ca");

    // --- COLLECT REVIEW DATA ---
    $name    = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : 'Anonymous';
    $rating  = isset($_POST['rating']) ? strip_tags($_POST['rating']) : 'No Rating';
    $message = isset($_POST['review']) ? strip_tags(trim($_POST['review'])) : 'No comment provided';

    // --- CONSTRUCT EMAIL ---
    $receiving_email = 'support@cargent.ca';
    $subject = "NEW CUSTOMER REVIEW: $rating Stars from $name";
    
    $email_body = "You have a new review for CarGent Mobile:\n\n";
    $email_body .= "Customer: $name\n";
    $email_body .= "Rating:   $rating Stars\n";
    $email_body .= "Feedback: $message\n\n";
    $email_body .= "===========================================";

    $headers = "From: CarGent Reviews <no-reply@cargent.ca>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // --- SEND & RESPOND ---
    // We use @ to prevent local XAMPP connection errors from breaking the response
    if (@mail($receiving_email, $subject, $email_body, $headers)) {
        http_response_code(200);
        echo "Review Submitted Successfully";
    } else {
        // Fallback for local testing so the "Thank You" box still shows
        http_response_code(200); 
        echo "Local Success (Email blocked by XAMPP but data received)";
    }

} else {
    http_response_code(404);
    echo "File Not Found";
}
?>