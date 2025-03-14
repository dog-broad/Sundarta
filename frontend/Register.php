<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/sundarta/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>


<body>
    <!-- Header -->
    <header class="bg-surface shadow-md">
        <nav class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-primary">sundarta
                    <span class="text-secondary">.</span>
                </a>
            </div>

            <!-- Navigation links -->
             <div class="hidden md-flex items-center gap-6">
                <a href=""></a>
             </div>
        </nav>
    </header>

    <!-- Login form -->
<div class="flex justify-center">
    <div class="form-container mx-auto w-96 p-8 mt-20 bg-sand shadow-md rounded-md">
        <h2 class="text-4xl font-bold mb-6 text-center">Register</h2>
        <form action="/sundarta/api/users/login" method="POST" class="space-y-6">
        <div class="input-group">
            <label for="username" class="input-label">Username</label>
            <input type="text" id="username" name="username" class="input-text w-full" placeholder="Enter username" required>
        </div>
        <div class="input-group">
            <label for="email" class="input-label">Email</label>
            <input type="email" id="email" name="email" class="input-text w-full" placeholder="Enter email" required >
        </div>
        <div class="input-group">
            <label for="phone" class="input-label">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="input-text w-full" placeholder="Enter phone number" required>
        </div>
        <div class="input-group">
            <label for="password" class="input-label">Password</label>
            <input type="password" id="password" name="password" class="input-text w-full" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>
    </div>
</div>
</body>
</html>


