document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.alert');
  if (!alerts || alerts.length === 0) return;

  const delay = 2500;
  setTimeout(() => {
    alerts.forEach(a => {
      a.style.transition = 'opacity 0.5s ease';
      a.style.opacity = '0';
      setTimeout(() => {
        if (a && a.parentNode) a.parentNode.removeChild(a);
      }, 600);
    });
  }, delay);
});