<?php
session_start();

$valid_username = "admin";
$valid_password = "12345";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['user'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "غلط یوزر نیم یا پاس ورڈ!";
    }
}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لاگ ان</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow-lg w-96 text-right">
    <h2 class="text-2xl font-bold mb-4 text-center">سسٹم میں لاگ ان کریں</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-center"><?= $error ?></div>
    <?php endif; ?>

    <label class="block mb-2">یوزر نیم</label>
    <input type="text" name="username" class="border p-2 w-full mb-4" required>

    <label class="block mb-2">پاس ورڈ</label>
    <input type="password" name="password" class="border p-2 w-full mb-4" required>

    <button class="bg-blue-600 text-white px-4 py-2 w-full rounded">لاگ ان</button>
  </form>
</body>
</html>
