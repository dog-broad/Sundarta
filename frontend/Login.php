<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/sundarta/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>


<body>
        <!-- Context 
 <h2 class="font-heading text-5xl mb-6 pb-2 border-b text-center mt-10 items-center">Revitalize Your Skin,       
    <br> Redefine Your Beauty.
    </h2>-->

    <!-- Header -->
    <header class="bg-surface shadow-md">
        <nav class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="/sundarta" class="text-2xl pl-5 font-bold text-primary">sundarta.
                </a>
            </div>

            <!-- Navigation links -->
             <div class="flex items-center gap-4">
                <a href="/sundarta/home" class="text-text-light hover:text-primary transition-colors h-5">
                <i class="fa-solid fa-house pr-3"></i>
                </a>
                
                <a href="/sundarta/Login" class="text-text-light hover:text-primary transition-colors h-5">
                    <i class="fa-solid fa-user-alt pr-5 text-[5pt]"></i>
                </a>
             </div>
        </nav>
    </header>

    <!-- Login form -->
<div class="flex justify-center">
    <div class="form-container mx-auto w-96 p-8 mt-20 mb-20 bg-sand shadow-md rounded-md">
        <h2 class="text-4xl font-bold mb-6 text-center">Login</h2>
        <form id="loginForm" class="space-y-6" onsubmit="event.preventDefault(); performLogin();">
          <div class="input-group">
            <label for="email" class="input-label">Email</label>
            <input type="email" id="email" name="email" class="input-text w-full" placeholder="Enter email" required >
        </div>
        
        <div class="input-group">
            <label for="password" class="input-label">Password</label>
            <input type="password" id="password" name="password" class="input-text w-full" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
async function performLogin() {
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  console.log('Attempting login with:', { email, password });

  const response = await fetch('/sundarta/api/users/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });

  const result = await response.json();
  console.log('Server response:', result);

  if (response.ok) {
    alert('Login successful! Welcome ' + result.username);
    window.location.href = '/sundarta/home';
  } else {
    alert(result.message || 'Invalid email or password. Please try again.');
  }
}
</script>
</body>

<!-- Footer -->
<?php
require 'partials/footer.php';
?>
</html>
