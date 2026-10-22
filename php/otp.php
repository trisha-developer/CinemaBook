<?php

session_start();

include "connection.php";

if (!isset($_SESSION['otp_email'])) {
    header("Location: register.php");
    exit;
}

if (isset($_POST['verify'])) {

    $email = $_SESSION['otp_email'];
    $otp = $_POST['otp'];

    $stmt = mysqli_prepare(
        $result,
        "SELECT * FROM register WHERE email = ? AND otp = ?"
    );

    mysqli_stmt_bind_param($stmt, "ss", $email, $otp);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $update = mysqli_prepare($result, "UPDATE register SET verified = 1, otp = NULL WHERE email = ?");

        mysqli_stmt_bind_param($update, "s", $email);
        mysqli_stmt_execute($update);

        unset($_SESSION['otp_email']);

        header("Location: login.php");
        exit;

    } 
    else{
        $error = "Invalid OTP.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
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

<h2>Enter OTP</h2>

<?php
if (isset($error)) {
    echo "<p>$error</p>";
}
?>

<form method="POST">
    <div class="input-group">
        <label for="otp">OTP</label>
        <input type="text" name="otp" placeholder="Enter OTP" maxlength="6">
</div>
        <button name="verify" class="auth-btn">Verify OTP</button>
</form>
</body>
</html>
