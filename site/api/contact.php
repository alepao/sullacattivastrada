<?php
    /*
     *  CONFIGURE EVERYTHING HERE
     */
    
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    
    //Import the PHPMailer class into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    
    // an email address that will be in the From field of the email.
    $fromEmail = 'postmaster@sullacattivastrada.net';
    $fromName = 'Servizio mail';
    
    // an email address that will receive the email with the output of the form
    $toEmail1 = '<postmaster@sullacattivastrada.net>';
    $toEmail2 = '<laura@sullacattivastrada.net>';
    $toEmail3 = '<lorenzo@sullacattivastrada.net>';
    
    // subject of the email
    $subject = 'Nuovo messaggio!!!';
    
    // form field names and their translations.
    // array variable name => Text to appear in the email
    //$fields = array('name' => 'Name', 'surname' => 'Surname', 'phone' => 'Phone', 'email' => 'Email', 'message' => 'Message');
    $fields = array('name' => 'Name', 'subject' => 'Subject', 'email' => 'Email', 'message' => 'Message');
    
    // message that will be displayed when everything is OK :)
    $okMessage = 'Contact form successfully submitted, thank you.';
    
    // If something goes wrong, we will display this message.
    $errorMessage = 'There was an error while submitting the form. Please try again later';
    
    /*
     *  LET'S DO THE SENDING
     */
    
    // if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
    error_reporting(E_ALL & ~E_NOTICE);
    
    try
    {
        
        if(count($_POST) == 0) throw new \Exception('Form is empty');
        
        $emailText = "Nuovo messaggio\n\n";
        
        foreach ($_POST as $key => $value) {
            // If the field exists in the $fields array, include it in the email
            if (isset($fields[$key])) {
                $emailText .= "$fields[$key]: $value\n";
            }
        }
        
        // All the neccessary headers for the email.
        $headers = array('Content-Type: text/plain; charset="UTF-8";',
                         'From: ' . $from,
                         'Reply-To: ' . $from,
                         'Return-Path: ' . $from,
                         );
        
        // smtp credentials and server
        
        $smtpHost = 'smtps.aruba.it';
        $smtpUsername = 'laura@sullacattivastrada.net';
        $smtpPassword = 'laurarossa';
        
        $mail = new PHPMailer;
        
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail1); // you can add more addresses by simply adding another line with $mail->addAddress();
        $mail->addAddress($toEmail2); // you can add more addresses by simply adding another line with $mail->addAddress();
        $mail->addAddress($toEmail3); // you can add more addresses by simply adding another line with $mail->addAddress();
        $mail->addReplyTo($from);
        $mail->Subject = $subject;
        
        $mail->isSMTP();
        
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        
        //Set the hostname of the mail server
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //$mail->Host = gethostbyname($smtpHost);
        $mail->Host = $smtpHost;
        
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 465;
        
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'ssl';
        
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        
        //Username to use for SMTP authentication - use full email address for gmail
        //We have configured this variable in the config section
        $mail->Username = $smtpUsername;
        
        //Password to use for SMTP authentication
        //We have configured this variable in the config section
        $mail->Password = $smtpPassword;
        
        
        
        $emailTextHtml = "<h1>You have a new message from your contact form</h1><hr>";
        $emailTextHtml .= "<table>";
        
        foreach ($_POST as $key => $value) {
            // If the field exists in the $fields array, include it in the email
            if (isset($fields[$key])) {
                $emailTextHtml .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
            }
        }
        $emailTextHtml .= "</table><hr>";
        $emailTextHtml .= "<p>Have a nice day,<br>Best,<br>Ondrej</p>";
        
        $mail->msgHTML($emailTextHtml); // this will also create a plain-text version of the HTML email, very handy
        $mail->AltBody = 'This is a plain-text message body';
        
        
        if(!$mail->send()) {
            echo $mail->ErrorInfo;
            throw new \Exception('I could not send the email.' . $mail->ErrorInfo);
        }
        
        // Send email
        //mail($sendTo, $subject, $emailText, implode("\n", $headers));
        //mail('postmaster@sullacattivastrada.net', 'asdasdas', 'asdasdasdadasda', 'dasdsadasdadsa');
        
        $responseArray = array('type' => 'success', 'message' => $okMessage);
    }
    catch (\Exception $e)
    {
        $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    }
    
    
    // if requested by AJAX request return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $encoded = json_encode($responseArray);
        
        header('Content-Type: application/json');
        
        echo $encoded;
    }
    // else just display the message
    else {
        echo $responseArray['message'];
    }

