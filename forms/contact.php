<?php
/**
 * CarGent Mobile - Unified Booking Controller (Individual & Fleet)
 * Updated: Feb 2026
 */

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Setup Admin Details
    $receiving_email_address = 'support@cargent.ca';
    $website_name = "CarGent Mobile";

    // 2. Identify the Booking Type
    $booking_type = isset($_POST['booking_type']) ? strip_tags($_POST['booking_type']) : 'Individual';

    // 3. Collect & Sanitize Common Data
    $name    = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : 'Web Customer';
    $phone   = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : 'Not Provided';
    $email   = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : 'No notes';

    // 4. Collect Type-Specific Data
    if ($booking_type === 'Fleet') {
        $company    = isset($_POST['company']) ? strip_tags(trim($_POST['company'])) : 'Not Provided';
        $fleet_size = isset($_POST['fleet_size']) ? strip_tags($_POST['fleet_size']) : 'Not Specified';
        $service    = "Fleet & Commercial Service";
        $subject    = "FLEET INQUIRY: " . $company . " - " . $name;
    } else {
        $service    = isset($_POST['service']) ? strip_tags($_POST['service']) : 'Not Selected';
        $location   = isset($_POST['location']) ? strip_tags(trim($_POST['location'])) : 'Not Provided';
        $subject    = "NEW SERVICE REQUEST: " . $service . " - " . $name;
    }

    // Clean subject line for security (remove newlines)
    $subject = str_replace(array("\r", "\n"), '', $subject);

    // 5. Validation Check
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please provide a valid email address.";
        exit;
    }

    // 6. Construct Email Body
    $email_body = "You have received a new $booking_type booking request from your website.\n\n";
    $email_body .= "==================================================\n";
    $email_body .= "CLIENT DETAILS\n";
    $email_body .= "==================================================\n";
    $email_body .= "Name:           $name\n";
    if ($booking_type === 'Fleet') {
        $email_body .= "Company:        $company\n";
        $email_body .= "Fleet Size:     $fleet_size\n";
    }
    $email_body .= "Phone:          $phone\n";
    $email_body .= "Email:          $email\n\n";
    
    $email_body .= "==================================================\n";
    $email_body .= "SERVICE DETAILS\n";
    $email_body .= "==================================================\n";
    $email_body .= "Request Type:   $booking_type\n";
    $email_body .= "Service Type:   $service\n";
    if ($booking_type === 'Individual') {
        $email_body .= "Location:       $location\n";
    }
    
    $email_body .= "\n==================================================\n";
    $email_body .= "ADDITIONAL NOTES\n";
    $email_body .= "==================================================\n";
    $email_body .= wordwrap($message, 70) . "\n\n";
    $email_body .= "==================================================";

    // 7. Email Headers
    // Note: 'From' uses a domain email to prevent spam filters from blocking it
    $headers = "From: $website_name <no-reply@cargent.ca>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 8. Send Email and Respond
    if(mail($receiving_email_address, $subject, $email_body, $headers)) {
        http_response_code(200);
        echo "Success";
    } else {
        http_response_code(500);
        echo "Server failed to send email.";
    }

} else {
    // Redirect if accessed directly
    header("Location: ../contact.html");
    exit;
}
?>