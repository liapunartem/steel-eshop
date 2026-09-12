const fields = document.querySelectorAll('.form__field');

fields.forEach(field => {
    const input = field.querySelector('input, textarea, select');

    if (!input) return;

    function updateLabel() {
        field.classList.toggle(
            'is-active',
            input.value.trim() !== '' || document.activeElement === field
        );
    }

    field.addEventListener('input', updateLabel);
    field.addEventListener('focus', updateLabel);
    field.addEventListener('blur', updateLabel);

    updateLabel();
});