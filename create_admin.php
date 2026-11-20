
<?php
require_once 'functions.php';

$name = 'System Administrator';
$email = 'admin@autocare.test';
$password = 'Admin@12345';


$check = $pdo->prepare(
    "SELECT id FROM users WHERE email = ?"
);
$check->execute([$email]);

if ($check->fetch()) {
    die("Admin account already exists!");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (?, ?, ?, 'admin')"
);

$stmt->execute([$name, $email, $hashed_password]);

echo "Admin account created successfully!";
echo "<br>Email: " . htmlspecialchars($email);
echo "<br>Password: " . htmlspecialchars($password);
?>
