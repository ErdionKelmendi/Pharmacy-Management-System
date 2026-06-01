<?php
session_start();

$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dirPath = str_replace('\\', '/', dirname(__DIR__));
$root = rtrim(str_replace($docRoot, '', $dirPath), '/');

if (isset($_SESSION['user_id'])) { 
    header('Location: ' . $root . '/index.php'); 
    exit; 
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$name || !$email || !$password) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $user = new User();
        $registered = $user->register($name, $email, $password);
        
        if ($registered) {
            $success = 'Account created! You can now log in.';
        } else {
            $error = 'Email already exists or registration failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sign Up — Pharmacy</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style> body { font-family:'Inter',sans-serif; } </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4">

  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-800/20 rounded-full blur-3xl"></div>
  </div>

  <div class="relative w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="h-1.5 bg-gradient-to-r from-blue-500 to-blue-700"></div>

      <div class="px-8 py-8">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">💊</div>
          <div>
            <div class="text-slate-900 font-bold text-xl leading-tight">Pharmacy</div>
            <div class="text-slate-400 text-xs tracking-widest uppercase">Management System</div>
          </div>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Create Account</h2>
        <p class="text-slate-500 text-sm mb-6">Join the management system today</p>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5 text-sm text-red-700 flex items-center gap-2">
          <span>⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-5 text-sm text-green-700 flex items-center gap-2">
          <span>✅</span> <?= htmlspecialchars($success) ?>
          <a href="login.php" class="underline font-bold ml-1">Login here</a>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
            <input type="text" name="name" required autofocus
              value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
              placeholder="Name Surname"
              class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
            <input type="email" name="email" required
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              placeholder="you@pharmacy.com"
              class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
            <input type="password" name="password" required placeholder="••••••••"
              class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"/>
          </div>

          <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-md shadow-blue-200 text-sm flex items-center justify-center gap-2 mt-2">
            Register Now →
          </button>
        </form>

        <div class="mt-6 text-center">
          <p class="text-slate-500 text-xs">Already have an account? 
            <a href="login.php" class="text-blue-600 font-bold hover:underline">Sign In</a>
          </p>
        </div>

      </div>
    </div>
    <p class="text-center text-slate-500 text-xs mt-6">© <?= date('Y') ?> Pharmacy Management System</p>
  </div>

</body>
</html>