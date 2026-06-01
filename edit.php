<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: auth/login.php'); exit; }
if ($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Medicine.php';

$medicine = new Medicine();
$id = (int)($_GET['id'] ?? 0);
$med = $medicine->getById($id);
if (!$med) { $_SESSION['flash']=['type'=>'error','msg'=>'Medicine not found.']; header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=$_POST['name']??''; $price=$_POST['price']??''; $qty=$_POST['quantity']??''; $exp=$_POST['expiry_date']??'';
    if (!$name)                              $errors[]='Medicine name is required.';
    if (!is_numeric($price)||$price<0)       $errors[]='Valid price is required.';
    if (!is_numeric($qty)||$qty<0)           $errors[]='Valid quantity is required.';
    if (!$exp)                               $errors[]='Expiry date is required.';
    if (!$errors) {
        $medicine->update($id,$name,(float)$price,(int)$qty,$exp);
        $_SESSION['flash']=['type'=>'success','msg'=>"Medicine '$name' updated."];
        header('Location: index.php'); exit;
    }
    $med=array_merge($med,compact('name','price')+['quantity'=>$qty,'expiry_date'=>$exp]);
}
$pageTitle='Edit Medicine';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900">Edit Medicine</h2>
      <p class="text-slate-500 text-sm mt-0.5"><?= htmlspecialchars($med['name']) ?></p>
    </div>
    <a href="index.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">← Back</a>
  </div>

  <?php foreach ($errors as $e): ?>
  <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2">⚠️ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm max-w-xl">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-slate-800">✏️ Medicine Details</h3>
      <span class="text-xs bg-slate-100 text-slate-500 font-semibold px-2.5 py-1 rounded-full">ID #<?= $id ?></span>
    </div>
    <div class="px-6 py-6">
      <form method="POST" class="space-y-5">
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Medicine Name *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($med['name']) ?>"
                 class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Price (USD) *</label>
            <input type="number" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($med['price']) ?>"
                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Quantity *</label>
            <input type="number" name="quantity" min="0" required value="<?= htmlspecialchars($med['quantity']) ?>"
                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Expiry Date *</label>
          <input type="date" name="expiry_date" required value="<?= htmlspecialchars($med['expiry_date']) ?>"
                 class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
        </div>
        <div class="flex gap-3 pt-2 border-t border-slate-100">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors shadow-sm">💾 Save Changes</button>
          <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
