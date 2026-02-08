<?php
session_start();
include_once("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $user, $db_password);
        $stmt->fetch();

        $_SESSION['mla_loggedin'] = true;
        $_SESSION['mla_user'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>
