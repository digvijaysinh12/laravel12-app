export const setupNotificationDropdown = (root) => {
    const button = root.querySelector('#notificationBtn');
    const dropdown = root.querySelector('#notificationDropdown');
    const markAllButton = root.querySelector('#markAllNotificationsBtn');

    if (!button || !dropdown) {
        return;
    }

    button.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    return {
        button,
        dropdown,
        markAllButton,
    };
};
