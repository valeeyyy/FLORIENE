<?php
session_start();
$aksi = $_REQUEST['aksi'] ?? '';

if ($aksi === 'logout') {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if ($aksi === 'status') {
    header('Content-Type: application/json');
    echo json_encode([
        'loggedIn' => isset($_SESSION['id_user']),
        'id_user' => $_SESSION['id_user'] ?? null,
        'nama' => $_SESSION['nama'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ]);
    exit();
}

include 'db.php';

if ($aksi === 'register') {
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $conn->real_escape_string($_POST['password'] ?? '');
    $nomor_hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');

    if ($conn->query("SELECT id_user FROM user WHERE email='$email'")->num_rows > 0) {
        header("Location: ../auth.html?error=email_exists");
        exit();
    }

    $now = date('Y-m-d H:i:s');
    $conn->query("INSERT INTO user (nama, email, password, nomor_hp, role, created_at)
                  VALUES ('$nama', '$email', '$password', '$nomor_hp', 'CUSTOMER', '$now')");
    $id_user = $conn->insert_id;
    $conn->query("INSERT INTO loyalty_card (id_user, poin, last_update_poin, created_at, updated_at)
                  VALUES ($id_user, 0, '$now', '$now', '$now')");

    header("Location: ../auth.html?success=1");
    exit();
}

$identifier = $conn->real_escape_string($_POST['identifier'] ?? '');
$password = $conn->real_escape_string($_POST['password'] ?? '');
$result = $conn->query("SELECT * FROM user WHERE (email='$identifier' OR nama='$identifier') AND password='$password' LIMIT 1");

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = strtolower($user['role']);
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin.php");
    } else {
        header("Location: ../profile.html");
    }
    exit();
}

header("Location: ../auth.html?error=1");
exit();
?>