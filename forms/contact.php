<?php
/**
 * CarGent Mobile - Unified Booking Controller (Individual & Fleet)
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Setup Admin Details
    $receiving_email_address = 'support@cargent.ca';
    $website_name = "CarGent Mobile";

    // 2. Identify the Booking Type (Fleet or Individual)
    $booking_type = isset($_POST['booking_type']) ? strip_tags($_POST['booking_type']) : 'Individual';

    // 3. Collect Common Data
    $name    = isset($_POST['name']) ? strip_tags($_POST['name']) : 'Web Customer';
    $phone   = isset($_POST['phone']) ? strip_tags($_POST['phone']) : 'Not Provided';
    $email   = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? strip_tags($_POST['message']) : 'No notes';

    // 4. Collect Type-Specific Data
    if ($booking_type === 'Fleet') {
        $company    = isset($_POST['company']) ? strip_tags($_POST['company']) : 'Not Provided';
        $fleet_size = isset($_POST['fleet_size']) ? strip_tags($_POST['fleet_size']) : 'Not Specified';
        $service    = "Fleet & Commercial Service";
        $subject    = "FLEET INQUIRY: " . $company . " - " . $name;
    } else {
        $service    = isset($_POST['service']) ? strip_tags($_POST['service']) : 'Not Selected';
        $location   = isset($_POST['location']) ? strip_tags($_POST['location']) : 'Not Provided';
        $subject    = "NEW SERVICE REQUEST: " . $service . " - " . $name;
    }

    // 5. Construct Email Body
    $email_body = "You have received a new $booking_type booking request from your website.\n\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "CLIENT DETAILS\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "Name:           $name\n";
    if ($booking_type === 'Fleet') {
        $email_body .= "Company:        $company\n";
        $email_body .= "Fleet Size:     $fleet_size\n";
    }
    $email_body .= "Phone:          $phone\n";
    $email_body .= "Email:          $email\n\n";
    
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "SERVICE DETAILS\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "Request Type:   $booking_type\n";
    $email_body .= "Service Type:   $service\n";
    if ($booking_type === 'Individual') {
        $email_body .= "Location:       $location\n";
    }
    
    $email_body .= "\n--------------------------------------------------\n";
    $email_body .= "ADDITIONAL NOTES\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "$message\n\n";
    $email_body .= "--------------------------------------------------";

    // 6. Email Headers
    // Note: On most live servers, 'From' should use an email associated with the domain
    $headers = "From: $website_name <no-reply@cargent.ca>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 7. Send Email and Respond
    if(mail($receiving_email_address, $subject, $email_body, $headers)) {
        // Since we are using JavaScript/AJAX in the HTML to show a thank you message, 
        // we just need to send a simple success code.
        http_response_code(200);
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error";
    }

} else {
    header("Location: ../contact.html");
    exit;
}
?>