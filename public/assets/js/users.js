  const roleLabels = {
    'all': 'All Users',
    'admin': 'Admins',
    'teacher': 'Teachers',
    'therapist': 'Therapists',
    'nurse': 'Nurses',
    'parent': 'Parents',
    'boarding_staff': 'Boarding Staff',
    'security_guard': 'Security'
  };

  function filterTab(role) {
    // Update active tab
    document.querySelectorAll('.section-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');

    // Update heading
    document.getElementById('table-heading').textContent = roleLabels[role] || 'All Users';

    // Show/hide rows
    document.querySelectorAll('.user-row').forEach(row => {
      if (role === 'all' || row.dataset.role === role) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }