document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const icon = toggleButton.querySelector('i');

    function updateTogglePosition() {
        if (sidebar.classList.contains('collapsed')) {
            toggleButton.style.left = '0';
            icon.classList.remove('bi-list');
            icon.classList.add('bi-chevron-right');
        } else {
            toggleButton.style.left = '0px';
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-list');
        }
    }

    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'collapsed') {
        sidebar.classList.add('collapsed');
    } else {
        sidebar.classList.remove('collapsed');
    }

    updateTogglePosition();
    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        updateTogglePosition();

        const currentState = sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded';
        localStorage.setItem('sidebarState', currentState);
    });
});
