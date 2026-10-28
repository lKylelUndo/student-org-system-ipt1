import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('nav-toggle');
    const mobileMenu = document.getElementById('nav-menu-mobile');
    const iconOpen = document.getElementById('nav-icon-open');
    const iconClose = document.getElementById('nav-icon-close');

    if (toggle && mobileMenu) {
        toggle.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden', isOpen);
            mobileMenu.classList.toggle('flex', !isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
            iconOpen?.classList.toggle('hidden', !isOpen);
            iconClose?.classList.toggle('hidden', isOpen);
        });
    }
});