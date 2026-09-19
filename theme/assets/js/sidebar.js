// Hiding/showing the sidebar on the front page
function initSidebar() {
    const gridWithSidebar = document.querySelector('.sidebar-layout');
    const sidebarToggleBtn = document.querySelector('.sidebar-toggle-button, .header__categories-button');

    if (!gridWithSidebar || !sidebarToggleBtn) {
        return;
    }

    sidebarToggleBtn.addEventListener('click', () => {
        gridWithSidebar.classList.toggle('sidebar-hidden');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
} else {
    initSidebar();
}

