document.addEventListener('click', function (event) {
  const button = event.target.closest('[data-toggle-password]');
  if (!button) return;
  const field = document.getElementById(button.getAttribute('data-toggle-password'));
  if (!field) return;
  const show = field.type === 'password';
  field.type = show ? 'text' : 'password';
  button.textContent = show ? 'Hide' : 'Show';
  button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
});
