<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: auth/login.php'); exit; }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Customer.php';

$customer  = new Customer();
$customers = $customer->getWithSaleCount();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$pageTitle = 'Customers';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="p-6 space-y-5">

  <div class="flex items-start justify-between">
    <div>
      <h2 class="text-2xl font-bold text-slate-900">Customers</h2>
      <p class="text-slate-500 text-sm mt-0.5"><?= count($customers) ?> registered customers</p>
    </div>
    <?php if ($_SESSION['role']==='admin'): ?>
    <a href="create.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow transition-colors">＋ Add Customer</a>
    <?php endif; ?>
  </div>

  <?php if ($flash): ?>
  <div class="alert-auto px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2
    <?= $flash['type']==='success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' ?>">
    <?= $flash['type']==='success' ? '✅' : '❌' ?> <?= htmlspecialchars($flash['msg']) ?>
  </div>
  <?php endif; ?>

  <?php if (empty($customers)): ?>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm py-20 text-center">
    <div class="text-5xl mb-4">👥</div>
    <div class="text-lg font-bold text-slate-700 mb-1">No customers yet</div>
    <div class="text-slate-400 text-sm">Add your first customer to get started.</div>
    <?php if ($_SESSION['role']==='admin'): ?>
    <a href="create.php" class="inline-block mt-4 bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl">Add Customer</a>
    <?php endif; ?>
  </div>

  <?php else: ?>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
          <tr>
            <th class="px-5 py-3.5 text-left font-semibold">#</th>
            <th class="px-5 py-3.5 text-left font-semibold">Name</th>
            <th class="px-5 py-3.5 text-left font-semibold">Phone</th>
            <th class="px-5 py-3.5 text-left font-semibold">Purchases</th>
            <th class="px-5 py-3.5 text-left font-semibold">Total Spent</th>
            <?php if ($_SESSION['role']==='admin'): ?><th class="px-5 py-3.5 text-left font-semibold">Actions</th><?php endif; ?>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php $colors=['bg-blue-500','bg-emerald-500','bg-violet-500','bg-rose-500','bg-amber-500'];
          foreach ($customers as $i => $c): ?>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-5 py-3.5 text-slate-400 text-xs"><?= $i+1 ?></td>
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full <?= $colors[$i%count($colors)] ?> flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                  <?= strtoupper(substr($c['name'],0,1)) ?>
                </div>
                <span class="font-semibold text-slate-800"><?= htmlspecialchars($c['name']) ?></span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-slate-500"><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                <?= $c['sale_count'] ?> purchase<?= $c['sale_count']!=1?'s':'' ?>
              </span>
            </td>
            <td class="px-5 py-3.5 font-bold text-green-600">$<?= number_format($c['total_spent'],2) ?></td>
            <?php if ($_SESSION['role']==='admin'): ?>
            <td class="px-5 py-3.5">
              <form method="POST" action="delete.php" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($c['name'])) ?>?')">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit"
                  class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                  🗑 Delete
                </button>
              </form>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
