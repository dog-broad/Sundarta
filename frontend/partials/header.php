<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sundarta - Beauty and Wellness</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="/sundarta/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold text-gray-800">Sundarta</div>
                <div class="flex items-center gap-4">
                    <a href="/sundarta/" class="text-gray-800 hover:text-gray-600">Home</a>
                    <a href="/sundarta/products" class="text-gray-800 hover:text-gray-600">Products</a>
                    <a href="/sundarta/services" class="text-gray-800 hover:text-gray-600">Services</a>
                    <a href="/sundarta/cart" class="text-gray-800 hover:text-gray-600">Cart</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/sundarta/profile" class="text-gray-800 hover:text-gray-600">Profile</a>
                        <a href="/sundarta/logout" class="text-gray-800 hover:text-gray-600">Logout</a>
                    <?php else: ?>
                        <a href="/sundarta/login" class="text-gray-800 hover:text-gray-600">Login</a>
                        <a href="/sundarta/register" class="text-gray-800 hover:text-gray-600">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main class="container mx-auto px-6 py-8">