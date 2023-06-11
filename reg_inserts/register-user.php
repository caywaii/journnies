<?php
session_start();

include '../config/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//PHP Mailer
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if (isset($_POST['register_btn'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $userType = 'Passenger';
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $municipality = $_POST['municipality'];
    $province = $_POST['province'];
    $idNumber = $_POST['idNumber'];
    $idType = $_POST['idType'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $promo = $_POST['promo'];

    //Validation of Email Existence
    $sql = "SELECT * FROM user WHERE uEmail ='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['status'] = "Email Already Exist!";
        header('Location:' . $home . '/registration-account.php');
        return;
    }

    //Register User in Users Table
    $sql_insert_user = "INSERT INTO user (uUserType, uUsername, uPassword, uEmail, uFirstName, uMiddleName, uLastName, uContact, uStreet, uBarangay, uCity, uProvince, uReferred, uIDType, uIDNumber) 
      VALUES ('$userType', '$username', '$password', '$email', '$firstName', '$middleName', '$lastName', '$contactNumber', '$street', '$barangay', '$municipality', '$province', '$promo', '$idType', '$idNumber');";
    $user_inserted = $conn->query($sql_insert_user);

    //Inserting Passenger
    $sql = "SELECT uID FROM user WHERE uEmail='$email' AND uPassword='$password'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $userID = $row['uID'];

  
    //Mailing Message
    $name = $firstName . " " . $lastName;
    $subject = "Vroom Vroom, Welcome! " . $name;
    $link = $home . "/config/verify-email.php?user=" . $email . "";
    $message = '
      <!DOCTYPE html>
      <html lang="en">
      <head>
      <meta charset="UTF-8">
      </head>
      <body>
      <h2>Welcome!</h2>
      <p>Please Verify your Email by cliking the link down below</p>
      <a id="verify" href="' . $link . '">Click this Link!</a>
      <p>Thank You!</p>
      </body>
      </html>
      ';


    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = 'true';
    $mail->Username = 'carpoolapp@caryl.tech';
    $mail->Password = 'carpool-App123';
    $mail->SMTPSecure = 'tls';
    $mail->Port = '587';

    $mail->setFrom('carpoolapp@caryl.tech', 'Carpool App');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->send();

    $_SESSION['status'] = "Check your Email for Verification";
    header('Location: ' . $home . '/index.php');
}
