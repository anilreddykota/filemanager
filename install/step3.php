<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: step2.php');
    exit;
}

$host = $_POST['db_host'];
$name = $_POST['db_name'];
$user = $_POST['db_user'];
$pass = $_POST['db_pass'];
$admin_user = $_POST['admin_user'];
$admin_email = $_POST['admin_email'];
$admin_pass = $_POST['admin_pass'];

// Step 1: Connect without DB
$serverConnection = mysqli_connect($host, $user, $pass);
if (!$serverConnection) {
    echo "<div class='alert alert-danger'>Connection failed: " . mysqli_connect_error() . "</div>";
    include 'includes/footer.php';
    exit;
}


// Step 2: Create database if not exists
$createDB = "CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if (!mysqli_query($serverConnection, $createDB)) {
    echo "<div class='alert alert-danger'>Database creation failed: " . mysqli_error($serverConnection) . "</div>";
    include 'includes/footer.php';
    exit;
}
mysqli_close($serverConnection);

// Step 3: Reconnect to the new DB
$dbConnection = mysqli_connect($host, $user, $pass, $name);
if (!$dbConnection) {
    echo "<div class='alert alert-danger'>Reconnection to DB failed: " . mysqli_connect_error() . "</div>";
    include 'includes/footer.php';
    exit;
}

// Step 4: Read and execute schema.sql
$sql = file_get_contents('schema.sql');
$queries = array_filter(array_map('trim', explode(';', $sql)));

$errors = [];

foreach ($queries as $query) {
    if (!empty($query)) {
        if (!mysqli_query($dbConnection, $query)) {
            $errors[] = mysqli_error($dbConnection);
        }
    }
}

// Step 4.1: Insert admin user

if (empty($admin_user) || empty($admin_email) || empty($admin_pass)) {
    $errors[] = "Admin username, email, and password are required.";
} elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid admin email format.";
}
$admin_email_status = 1; // Assuming email is verified
$hashedPassword = password_hash($admin_pass, PASSWORD_DEFAULT);
$insertAdmin = "INSERT INTO users (username, email, password, plan, email_status) VALUES (?, ?, ?, 'ultra', ?)";

$stmt = $dbConnection->prepare($insertAdmin);
$stmt->bind_param("sssi", $admin_user, $admin_email, $hashedPassword, $admin_email_status);

if (!$stmt->execute()) {
    $errors[] = "Failed to insert admin user: " . $stmt->error;
}

$stmt->close();


// Step 5: Save config
$configContent = "<?php\nreturn [\n  'db' => [\n    'host' => '$host',\n    'name' => '$name',\n    'user' => '$user',\n    'pass' => '$pass'\n  ]\n];";
file_put_contents('../config/config.php', $configContent);

//  set to .env file also 
// Update or add DB_* variables in .env without overwriting other data

$envPath = '../.env';
$envVars = [
    'DB_HOST' => $host,
    'DB_NAME' => $name,
    'DB_USER' => $user,
    'DB_PASS' => $pass
];

$envLines = [];
if (file_exists($envPath)) {
    $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envAssoc = [];
    foreach ($envLines as $line) {
        if (strpos(trim($line), '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            $envAssoc[trim($k)] = $v;
        }
    }
    foreach ($envVars as $k => $v) {
        $envAssoc[$k] = $v;
    }
    $newEnv = '';
    foreach ($envAssoc as $k => $v) {
        $newEnv .= "$k=$v\n";
    }
    file_put_contents($envPath, $newEnv);
} else {
    $envContent = '';
    foreach ($envVars as $k => $v) {
        $envContent .= "$k=$v\n";
    }
    file_put_contents($envPath, $envContent);
}


// Step 6: Show result
if (empty($errors)) {
    echo "
    <div class='card shadow mt-5' style='max-width: 500px; margin: 40px auto; border-radius: 12px;'>
        <div class='card-body text-center'>
            <div style='font-size: 3rem; color: #28a745; margin-bottom: 16px;'>🎉</div>
            <h2 class='card-title mb-3' style='color: #28a745;'>Installation Complete!</h2>
            <p class='card-text mb-4'>Your application has been successfully installed.<br>
            You can now proceed to the dashboard.</p>
            <a href='done.php' class='btn btn-success btn-lg px-5'>Continue &rarr;</a>
        </div>
    </div>
    ";
} else {
    echo "<div class='alert alert-danger'><strong>Errors occurred during installation:</strong><ul>";
    foreach ($errors as $err) echo "<li>" . htmlspecialchars($err) . "</li>";
    echo "</ul></div>";
}

mysqli_close($dbConnection);

include 'includes/footer.php';
