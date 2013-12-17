<?php
require("..\PHPMailer\PHPMailerAutoload.php");

session_start();
if(isset($_SESSION["loggeduser"]) && isset($_SESSION["loggeduser"]["nameuser"])) {
	$myname = $_SESSION["loggeduser"]["nameuser"];
} else {
	$myname="CoopEShop";
}

$mymail = $_GET["mailfrom"];
$message = $_GET["message"];		
$mailcontact = $_GET["mailto"];

$sujet = $_GET["subject"];

$mail = new PHPMailer;

$mail->isSMTP(); 
   
$mail->CharSet = 'UTF-8'; 
$mail->Port       = 587;                              // Set mailer to use SMTP
$mail->Host = 'smtp.edvariables.net';  				  // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'demo@coopeshop.net';               // SMTP username
$mail->Password = 'demo';                             // SMTP password
//$mail->SMTPSecure = 'tls';                          // Enable encryption, 'ssl' also accepted

$mail->addAddress($mailcontact);
$mail->From = $mymail;
$mail->addReplyTo($mymail);
$mail->FromName = $myname;

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');       // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');  // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $sujet;
$mail->Body    = $message;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		
$status = $mail->send();

$statusjso = '{ "Status" : "'. $status.'"'
		.', "Info" : "'.$mail->ErrorInfo.'" }';

echo $statusjso;
?>