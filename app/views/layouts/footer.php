    </main>
  </div>
  <script>
    function closeModal(modalId) {
      const modal = document.getElementById(modalId);
        if (modal)
          modal.classList.remove('open');
    }
    
    function openModal(modalId) {
      const modal = document.getElementById(modalId);
        if (modal) 
          modal.classList.add('open');
    }
  </script>
</body>
</html>