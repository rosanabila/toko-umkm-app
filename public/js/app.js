/**
 * TokoKita Client-side Interaction Helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    // Dynamic sidebar active marker transition
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // Alert auto-fadeout
    const alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

/**
 * Format number as Rupiah currency
 * @param {number} value 
 * @returns {string}
 */
function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

/**
 * Confirm delete helper
 * @param {Event} e 
 * @param {string} message 
 */
function confirmAction(e, message = 'Apakah Anda yakin ingin melakukan tindakan ini?') {
    if (!confirm(message)) {
        e.preventDefault();
        return false;
    }
    return true;
}
