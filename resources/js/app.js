import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Lucide icons after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Re-initialize icons after Alpine updates (for dynamically added content)
document.addEventListener('alpine:initialized', () => {
    createIcons({ icons });
});
