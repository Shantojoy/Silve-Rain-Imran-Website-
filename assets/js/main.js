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
      if (window.innerWidth <= 991) sidebar.classList.toggle('mobile-open');
      else sidebar.classList.toggle('collapsed');
    });
  }

  if (window.tinymce) {
    tinymce.init({
      selector: 'textarea.editor',
      height: 320,
      plugins: 'link image lists table code preview',
      toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code preview'
    });
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
});
