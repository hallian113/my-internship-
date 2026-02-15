<?php
/**
 * CarGent Mobile - Standard 4-Step Booking Controller
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Setup Admin Details
    $receiving_email_address = 'support@cargent.ca';
    $website_name = "CarGent Mobile";

    // 2. Collect Data from HTML names
    $service  = isset($_POST['service']) ? strip_tags($_POST['service']) : 'Not Selected';
    $location = isset($_POST['location']) ? strip_tags($_POST['location']) : 'Not Provided';
    $name     = isset($_POST['name']) ? strip_tags($_POST['name']) : 'Web Customer';
    $phone    = isset($_POST['phone']) ? strip_tags($_POST['phone']) : 'Not Provided';
    $email    = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $message  = isset($_POST['message']) ? strip_tags($_POST['message']) : 'No notes';

    // 3. Create Subject Line
    $subject = "NEW SERVICE REQUEST: " . $service . " - " . $name;

    // 4. Construct Email Body (The layout you will see in your inbox)
    $email_body = "You have received a new booking request from your website.\n\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "SERVICE DETAILS\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "Service Type:   $service\n";
    $email_body .= "Location:       $location\n\n";
    
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "CUSTOMER DETAILS\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "Name:           $name\n";
    $email_body .= "Phone:          $phone\n";
    $email_body .= "Email:          $email\n\n";
    
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "ADDITIONAL NOTES\n";
    $email_body .= "--------------------------------------------------\n";
    $email_body .= "$message\n\n";
    $email_body .= "--------------------------------------------------";

    // 5. Email Headers
    $headers = "From: $website_name <no-reply@cargent.ca>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 6. Send Email and Redirect
    if(mail($receiving_email_address, $subject, $email_body, $headers)) {
        // SUCCESS: Show an alert and go back to home
        echo "<script>
                alert('Thank you! Your request for $service has been sent to our team.');
                window.location.href = '../index.html';
              </script>";
    } else {
        // FAILURE
        echo "<script>
                alert('Error: We could not process your request. Please call us directly at +1 (437) 335-9080.');
                window.history.back();
              </script>";
    }

} else {
    // If someone tries to access this file directly
    header("Location: ../contact.html");
    exit;
}
?>