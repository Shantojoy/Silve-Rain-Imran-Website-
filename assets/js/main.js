document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-confirm]').forEach((el) => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  const sidebar = document.getElementById('adminSidebar');
  const toggle = document.getElementById('sidebarToggle');
  if (sidebar && toggle) {
    toggle.addEventListener('click', () => {
      if (window.innerWidth <= 991) {
        sidebar.classList.toggle('mobile-open');
      } else {
        sidebar.classList.toggle('expanded');
      }
    });
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
});
