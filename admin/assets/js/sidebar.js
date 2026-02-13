document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.querySelector('.toggle-btn');

  // Collapse/Expand sidebar
  toggleBtn.addEventListener('click', () => {
    const isCollapsed = sidebar.classList.toggle('collapsed');

    if (isCollapsed) {
      // Close all open items
      const openItems = sidebar.querySelectorAll('.sidebar-item.open');
      openItems.forEach(item => {
        item.classList.remove('open');
      });

      // Collapse all dropdown ULs
      const dropdowns = sidebar.querySelectorAll('.sidebar-dropdown, .collapse');
      dropdowns.forEach(ul => {
        ul.classList.remove('show');
      });
    }
  });

  // Dropdowns
  const dropdownLinks = sidebar.querySelectorAll('.sidebar-link.has-dropdown');

  dropdownLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const parentItem = link.parentElement;

      // Expand sidebar if collapsed
      if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
      }

      // Toggle open state
      parentItem.classList.toggle('open');

      // Toggle the actual UL dropdown
      const dropdown = parentItem.querySelector('.sidebar-dropdown, .collapse');
      if (dropdown) {
        dropdown.classList.toggle('show');
      }
    });
  });
});
