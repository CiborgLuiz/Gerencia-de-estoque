import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const THEME_KEY = 'theme';

const applyTheme = (theme) => {
    document.documentElement.classList.toggle('dark', theme === 'dark');
};

const storedTheme = localStorage.getItem(THEME_KEY);
const initialTheme = storedTheme ?? 'dark';
applyTheme(initialTheme);

window.setTheme = (theme) => {
    localStorage.setItem(THEME_KEY, theme);
    applyTheme(theme);
};

Alpine.start();
