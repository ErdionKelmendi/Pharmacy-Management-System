<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: auth/login.php'); exit; }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Customer.php';

$errors=[]; $v=['name'=>'','phone'=>''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['name']=trim($_POST['name']??''); $v['phone']=trim($_POST['phone']??'');
    if (!$v['name']) $errors[]='Customer name is required.';
    if (!$errors) {
        (new Customer())->create($v['name'],$v['phone']);
        $_SESSION['flash']=['type'=>'success','msg'=>"Customer '{$v['name']}' added."];
        header('Location: index.php'); exit;
    }
}
$pageTitle='Add Customer';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900">Add New Customer</h2>
      <p class="text-slate-500 text-sm mt-0.5">Register a new customer</p>
    </div>
    <a href="index.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">← Back</a>
  </div>

  <?php foreach ($errors as $e): ?>
  <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2">⚠️ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm max-w-md">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="font-bold text-slate-800">👤 Customer Details</h3>
    </div>
    <div class="px-6 py-6">
      <form method="POST" class="space-y-5">
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Name *</label>
          <input type="text" name="name" required autofocus value="<?= htmlspecialchars($v['name']) ?>"
                 placeholder="Name Surname"
                 class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
          <input type="tel" name="phone" value="<?= htmlspecialchars($v['phone']) ?>"
                 placeholder="+383 00 000 000"
                 class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
        </div>
        <div class="flex gap-3 pt-2 border-t border-slate-100">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors shadow-sm">＋ Add Customer</button>
          <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
