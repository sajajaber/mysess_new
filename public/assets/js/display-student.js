document.querySelectorAll('.section-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.section-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.section-panel').forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    document.getElementById(tab.dataset.target).classList.add('active');
  });
});