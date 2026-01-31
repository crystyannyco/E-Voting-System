<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC E-Voting System</title>
    <!-- Import Poppins font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center font-poppins" style="background-image: url('<?= base_url('assets/background.png') ?>');">

    
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md z-12">
        <div class="flex justify-center mb-6 space-x-4">
            <img src="<?= base_url('assets/cspc_logo.png') ?>" alt="Logo 1" class="h-12 w-12 rounded-full">
            <img src="<?= base_url('assets\SSC Logo.png') ?>" alt="Logo 2" class="h-12 w-12 rounded-full">
        </div>
        
        <h1 class="text-3xl font-bold text-center text-blue-900 mb-2">CSPC E-Voting System</h1>
        
        <h2 class="text-2xl font-bold text-center text-blue-700 mb-2">Sign in</h2>
        <p class="text-center text-gray-500 mb-6 font-light">Please login to continue to your account.</p>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <!-- Update your login form with proper form action and method -->
        <form action="<?= base_url('login/process') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Email/Username field -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-600 mb-1">Email or Username</label>
                <input type="text" id="email" name="email" placeholder="Email or Username" value="<?= old('email') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Password field -->
            <div class="mb-6 relative">
                <label for="password" class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <!-- Password toggle button -->
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out">
                Sign in
            </button>
        </form>     

<script>
    // Script to toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        const eyeIcon = this.querySelector('svg');
        if (type === 'text') {
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
        } else {
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
            `;
        }
    });

    // Script to save email in localStorage when form is submitted
    document.querySelector('form').addEventListener('submit', function(e) {
        const emailInput = document.getElementById('email');
        if (emailInput.value) {
            localStorage.setItem('savedEmail', emailInput.value);
        }
    });

    // Script to load email from localStorage when page loads
    window.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        // Only set from localStorage if the field is empty (PHP old() takes precedence)
        if (emailInput.value === '') {
            const savedEmail = localStorage.getItem('savedEmail');
            if (savedEmail) {
                emailInput.value = savedEmail;
            }
        }
    });
</script>
</body>
</html>