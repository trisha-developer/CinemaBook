<?php

session_start();

include_once("connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$error = "";

if (isset($_POST['btn'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
       $error = "Passwords do not match.";
    } 
    else if(empty($username) || empty($email) || empty($password) || empty($confirm_password)){
        $error="All fields are required.";
    }
    else {
        $check = "SELECT s_no FROM register WHERE email = ?";
        $stmt = mysqli_prepare($result, $check);

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "This email is already registered.";
        } 
        else{
            $otp= rand(1000,9999);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $data_insert = "INSERT INTO register(username,email,password,otp) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($result, $data_insert);

            mysqli_stmt_bind_param($stmt,"ssss",$username,$email,$hashed_password,$otp);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['otp_email'] = $email;

                #sending mail

                $mail=new PHPMailer(true);

                try{

                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;

                    $mail->Username = 'hey.trisha.dev@gmail.com';
                    $mail->Password = 'qvcg hpve rlay tmjf';

                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom(
                        'hey.trisha.dev@gmail.com',
                        'CinemaBook'
                    );

                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "CinemaBook OTP";
                    $mail->Body = "<div>
                    <h2>Please verify your identity, $username</h2>
                    <p>Here is your CinemaBook registration authentication code:
                    <h2>$otp</h2>
                    This code is valid for 15 minutes and can only be used once.
                    Please don't share this code with anyone: we'll never ask for it on the phone or via email.
                    Thanks,
                    The CinemaBook Team
                    </p></div>";

                    $mail->send();

                    header("Location: otp.php");
                    exit;

                } catch (Exception $e) {

                    $error = "OTP could not be sent.";

                }
                }
            } 
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | CinemaBook</title>
<link rel="stylesheet" href="style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
   <a href="book.php" class="logo"><span>▶</span> CinemaBook</a>
   <a href="book.php" class="back-home">← Back to home</a>
</nav>

<main class="auth-page">
<div class="auth-box register-box">
  <div class="auth-heading">
    <div class="auth-icon">🎟️</div>
      <h1>Create your account</h1>
      <p>Join CinemaBook and start booking your favourite movies.</p>
</div>

<?php if ($error != "") { ?>
    <p class="form-error"><?php echo $error; ?></p>
<?php } ?>

<form action="" method="post">
<div class="input-group">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" placeholder="Enter your username">
</div>

<div class="input-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Enter your email">
</div>

<div class="input-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Create a password">
</div>

<div class="input-group">
    <label for="confirm-password">Confirm Password</label>
    <input type="password" id="confirm-password" name="confirm_password" placeholder="Repeat your password">
</div>
    <button type="submit" class="auth-btn" name="btn">Create Account</button>
</form>
    <p class="switch-auth">Already have an account?<a href="login.php">Sign in</a></p>
</div>

</main>
</body>
</html>
