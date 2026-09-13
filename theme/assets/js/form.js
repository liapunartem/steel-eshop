document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // Floating / active labels for form fields
    // -------------------------------------------------------------------------
    const fields = document.querySelectorAll('.form__field');

    fields.forEach(field => {
        const input = field.querySelector('input, textarea, select');
        if (!input) return;

        function updateLabel() {
            field.classList.toggle(
                'is-active',
                input.value.trim() !== '' || document.activeElement === input
            );
        }

        input.addEventListener('input', updateLabel);
        input.addEventListener('change', updateLabel);
        input.addEventListener('focus', updateLabel);
        input.addEventListener('blur', updateLabel);

        updateLabel();
    });



    // -------------------------------------------------------------------------
    // Quick-switch between auth tabs ("Already have an account?" / "Register")
    // -------------------------------------------------------------------------
    document.addEventListener('click', (e) => {
        const switchLink = e.target.closest('[data-switch-to]');
        if (!switchLink) return;

        e.preventDefault();
        const targetId = switchLink.getAttribute('data-switch-to');
        if (!targetId) return;

        const tabTrigger = document.querySelector(`.tabs__item[data-target="${targetId}"] .tabs__link`);
        if (tabTrigger) {
            tabTrigger.click();

            setTimeout(() => {
                const panel = document.getElementById(targetId);
                if (panel) {
                    const firstInput = panel.querySelector('input:not([type="hidden"])');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }
            }, 60);
        }
    });
});