    </main>
    <footer class="app-footer">
      <p>© <?= date('Y') ?> <?= esc(APP_NAME) ?>. All rights reserved.</p>
    </footer>
  </div>
  <script>
    function closeModal(modalId) {
      document.getElementById(modalId)?.classList.remove('open');
    }
  </script>
</body>
</html>
