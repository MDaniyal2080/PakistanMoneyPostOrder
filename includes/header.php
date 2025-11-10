<?php
// includes/header.php
session_start();
if (!isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">
  <?php if (isset($_SESSION['user'])): ?>
  <!-- Navbar -->
  <nav class="bg-blue-700 text-white px-8 py-4 shadow-md">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <span class="text-2xl font-bold tracking-wide">Order System</span>
      </div>

      <div class="hidden md:flex items-center space-x-6">
        <a href="dashboard.php" class="hover:text-gray-200 transition">Dashboard</a>
        <a href="products.php" class="hover:text-gray-200 transition">Products</a>
        <a href="create-order.php" class="hover:text-gray-200 transition">New Order</a>
        <a href="reports.php" class="hover:text-gray-200 transition">Reports</a>
        <a href="logout.php" class="hover:text-gray-200 transition">Logout</a>
      </div>

      <!-- Mobile Menu (Hamburger) -->
      <div class="md:hidden">
        <button id="menu-btn" class="focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown -->
    <div id="mobile-menu" class="hidden md:hidden bg-blue-800">
      <a href="dashboard.php" class="block px-6 py-3 hover:bg-blue-600">Dashboard</a>
      <a href="products.php" class="block px-6 py-3 hover:bg-blue-600">Products</a>
      <a href="create-order.php" class="block px-6 py-3 hover:bg-blue-600">New Order</a>
      <a href="reports.php" class="block px-6 py-3 hover:bg-blue-600">Reports</a>
      <a href="logout.php" class="block px-6 py-3 hover:bg-blue-600">Logout</a>
    </div>
  </nav>

  <script>
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
  <?php endif; ?>

  <div class="p-6 max-w-7xl mx-auto">
