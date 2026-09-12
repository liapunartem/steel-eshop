// Hiding the sidebar
const gridWithSidebar = document.querySelector('.sidebar-layout');
const sidebarToggleBtn = document.querySelector('.sidebar-toggle-button');

sidebarToggleBtn?.addEventListener('click', () => {
    gridWithSidebar?.classList.toggle('sidebar-hidden');
});

