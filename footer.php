<footer class="bg-white border-t border-slate-200 mt-auto">
  <div class="px-6 py-4 flex items-center justify-between text-xs text-slate-400">
    <span>© <?= date('Y') ?> Pharmacy Management System</span>
    <span>Logged in as <strong class="text-slate-600"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?></strong> · <?= ucfirst($_SESSION['role'] ?? '') ?></span>
  </div>
</footer>

</div>
</div>

<script>
document.querySelectorAll('.alert-auto').forEach(el => {
  setTimeout(() => { el.style.transition='opacity .4s'; el.style.opacity='0'; setTimeout(()=>el.remove(),400); }, 4500);
});
</script>
</body>
</html>
