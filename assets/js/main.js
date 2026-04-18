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
    });
  }

  if (window.ClassicEditor) {
    const editorMap = new Map();
    document.querySelectorAll('textarea.editor').forEach((el) => {
      if (!el.dataset.editorInit) {
        ClassicEditor.create(el)
          .then((editor) => editorMap.set(el, editor))
          .catch((error) => console.error(error));
        el.dataset.editorInit = "1";
      }
    });

    document.querySelectorAll('form').forEach((form) => {
      form.addEventListener('submit', () => {
        editorMap.forEach((editor) => {
          editor.updateSourceElement();
        });
      });
    });
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
});
