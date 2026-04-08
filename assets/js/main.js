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

  if (window.ClassicEditor) {
    document.querySelectorAll('textarea.editor').forEach((el) => {
      if (!el.dataset.editorInit) {
        ClassicEditor.create(el).catch((error) => console.error(error));
        el.dataset.editorInit = "1";
      }
    });
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
});
