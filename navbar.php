<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dirPath = str_replace('\\', '/', dirname(__DIR__));

$root = rtrim(str_replace($docRoot, '', $dirPath), '/');
$base = $root . '/';

$cur     = $_SERVER['PHP_SELF'];
$uName   = $_SESSION['user_name'] ?? 'User';
$uRole   = $_SESSION['role']      ?? 'user';
$uInit   = strtoupper(substr($uName, 0, 1));
$isAdmin = $uRole === 'admin';

function nav(string $href, string $icon, string $label, string $cur): void {
    $path   = parse_url($href, PHP_URL_PATH);
    $active = str_contains($cur, $path)
              ? 'bg-blue-600 text-white'
              : 'text-slate-300 hover:bg-slate-700 hover:text-white';
    echo "<a href=\"$href\" class=\"flex items-center gap-3 px-3 py-2.5 rounded-lg $active transition-all text-sm font-medium\">
            <span class=\"text-base\">$icon</span> $label
          </a>";
}
?>

<div class="flex min-h-screen">

<aside class="w-64 bg-slate-900 flex flex-col fixed top-0 left-0 h-full z-40 shadow-2xl">

  <div class="px-5 py-5 border-b border-slate-700/60">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-lg">💊</div>
      <div>
        <div class="text-white font-bold text-base leading-tight">Pharmacy</div>
        <div class="text-slate-400 text-[10px] tracking-widest uppercase">Management System</div>
      </div>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1 pt-2">Overview</p>
    <?php nav($base.'index.php',              '🏠', 'Dashboard',    $cur); ?>

    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1 pt-3">Inventory</p>
    <?php nav($base.'medicines/index.php',    '💊', 'Medicines',    $cur); ?>
    <?php if ($isAdmin) nav($base.'medicines/create.php', '➕', 'Add Medicine', $cur); ?>

    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1 pt-3">Sales</p>
    <?php nav($base.'sales/create.php',       '🛒', 'New Sale',      $cur); ?>
    <?php nav($base.'sales/index.php',        '🧾', 'Sales History', $cur); ?>

    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1 pt-3">Customers</p>
    <?php nav($base.'customers/index.php',    '👥', 'Customers',     $cur); ?>
    <?php nav($base.'customers/create.php', '➕', 'Add Customer', $cur); ?>
  </nav>

  <div class="px-3 py-4 border-t border-slate-700/60">
    <div class="flex items-center gap-3 bg-slate-800 rounded-xl px-3 py-2.5">
      <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
        <?= htmlspecialchars($uInit) ?>
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-white text-sm font-semibold truncate"><?= htmlspecialchars($uName) ?></div>
        <div class="text-slate-400 text-xs capitalize"><?= htmlspecialchars($uRole) ?></div>
      </div>
      <a href="<?= $base ?>auth/logout.php" title="Logout"
         class="text-slate-400 hover:text-red-400 transition-colors text-lg ml-1">⇥</a>
    </div>
  </div>

</aside>

<div class="flex-1 ml-64 flex flex-col min-h-screen">

<header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
  <div class="flex items-center justify-between px-6 h-16">
    <h1 class="text-slate-800 font-bold text-lg"><?= $pageTitle ?? 'Dashboard' ?></h1>
    <a href="<?= $base ?>sales/create.php"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
      ＋ New Sale
    </a>
  </div>
</header>
