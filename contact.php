<?php
  /**
  * CarGent Mobile - 4-Step Booking Controller (Gold & Black Optimized)
  */

  $receiving_email_address = 'support@cargent.ca';

  // Correct path to the library
  $php_email_form_path = '../assets/vendor/php-email-form/php-email-form.php';

  if( file_exists($php_email_form_path) ) {
    include( $php_email_form_path );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;

  // Set basic email headers
  $contact->to = $receiving_email_address;
  $contact->from_name = isset($_POST['name']) ? $_POST['name'] : 'Web Customer';
  $contact->from_email = isset($_POST['email']) ? $_POST['email'] : 'no-email@cargent.ca';
  
  // Create a clean subject line based on the selected service
  $service_selected = isset($_POST['service']) ? $_POST['service'] : 'General Inquiry';
  $contact->subject = "SERVICE REQUEST: " . $service_selected;

  // MAPPING THE 4 STEPS
  // These will now automatically include any of the 6 services you added to HTML
  $contact->add_message( $service_selected,   'Step 1: Requested Service');
  $contact->add_message( $_POST['location'], 'Step 2: Vehicle Location');
  $contact->add_message( $_POST['name'],     'Step 3: Customer Name');
  $contact->add_message( $_POST['phone'],    'Step 3: Phone Number');
  $contact->add_message( $_POST['email'],    'Step 4: Receipt Email');
  $contact->add_message( $_POST['message'],  'Step 4: Additional Notes', 10);

  echo $contact->send();
?>