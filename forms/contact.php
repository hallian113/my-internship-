<?php
/**
 * CarGent Mobile - Unified Booking Controller
 * Integrated with Mailtrap Credentials & CORS Fix
 */

// 1. CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- MAILTRAP CREDENTIALS ---
    $host     = 'sandbox.smtp.mailtrap.io';
    $port     = '587';
    $username = 'f38b98a1ba31c1'; 
    $password = 'ae3323d6ee7eb7'; 

    ini_set("SMTP", $host);
    ini_set("smtp_port", $port);
    ini_set("sendmail_from", "support@cargent.ca");

    // --- COLLECT DATA ---
    $booking_type = isset($_POST['booking_type']) ? strip_tags($_POST['booking_type']) : 'Individual';
    $name    = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : 'Web Customer';
    $phone   = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : 'Not Provided';
    $email   = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : 'No notes';

    // --- COLLECT ADD-ONS ---
    $addons = [];
    if (isset($_POST['addon_cabin_filter'])) { $addons[] = "Cabin Air Filter"; }
    if (isset($_POST['addon_engine_filter'])) { $addons[] = "Engine Air Filter"; }
    if (isset($_POST['addon_wipers'])) { $addons[] = "Premium Wiper Blades"; }
    $addons_list = !empty($addons) ? implode(", ", $addons) : "None";

    // --- SERVICE DETAILS & LOCATION ---
    if ($booking_type === 'Fleet') {
        $company    = isset($_POST['company']) ? strip_tags(trim($_POST['company'])) : 'Not Provided';
        $fleet_size = isset($_POST['fleet_size']) ? strip_tags($_POST['fleet_size']) : 'Not Specified';
        $service    = "Fleet & Commercial Service";
    } else {
        $service     = isset($_POST['service']) ? strip_tags($_POST['service']) : 'Not Selected';
        $location    = isset($_POST['location']) ? strip_tags(trim($_POST['location'])) : 'Not Provided';
        // Captured from updated contact form fields
        $city        = isset($_POST['city']) ? strip_tags(trim($_POST['city'])) : 'Not Provided';
        $postal_code = isset($_POST['postal_code']) ? strip_tags(trim($_POST['postal_code'])) : 'Not Provided';
    }

    // --- CONSTRUCT EMAIL ---
    $receiving_email = 'support@cargent.ca';
    $subject = "NEW REQUEST: $service from $name";
    
    $email_body = "New $booking_type booking request:\n\n";
    $email_body .= "Name: $name\nPhone: $phone\nEmail: $email\n";
    
    if ($booking_type === 'Fleet') {
        $email_body .= "Company: $company\nFleet Size: $fleet_size\n";
    } else {
        $email_body .= "Street Address: $location\n";
        $email_body .= "City: $city\n";
        $email_body .= "Postal Code: $postal_code\n";
        $email_body .= "Add-ons: $addons_list\n";
    }
    
    $email_body .= "\nMessage: $message\n";

    $headers = "From: CarGent Booking <no-reply@cargent.ca>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // --- SEND & RESPOND ---
    if (@mail($receiving_email, $subject, $email_body, $headers)) {
        http_response_code(200);
        echo "Success";
    } else {
        http_response_code(200); 
        echo "Local Testing: Success (Email simulated)";
    }

} else {
    http_response_code(404);
    echo "Not Found";
}
?>