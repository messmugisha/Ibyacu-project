<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Read form inputs safely
    $username   = $_POST['username'] ?? '';
    $email      = $_POST['email'] ?? '';
    $location   = $_POST['location'] ?? '';
    $contacts   = $_POST['contacts'] ?? '';
    $bio        = $_POST['bio'] ?? '';
    $pass       = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // -----------------------------
    // VALIDATION
    // -----------------------------

    // Check if passwords match
    if ($pass !== $confirm) {
        echo "<script>alert('Ijambo banga ntabwo rihuya!'); window.history.back();</script>";
        exit();
    }

    // Encrypt password
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // -----------------------------
    // DATABASE SAVE
    // -----------------------------
    $sql = "
        INSERT INTO users 
        (username, email, location, contacts, bio, password)
        VALUES 
        ('$username', '$email', '$location', '$contacts', '$bio', '$hash')
    ";

    if ($conn->query($sql)) {
        echo "<script>alert('Kwiyandikisha byagenze neza!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Habaye ikibazo: {$conn->error}'); window.history.back();</script>";
    }
}
?>
