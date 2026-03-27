import 'bootstrap';
import './products';

document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast');

    toasts.forEach(toast => {
        setTimeout(() => {
            toast.remove();
        }, 3000); // 3 seconds
    });
});