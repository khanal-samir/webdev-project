function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

function showConfirm(message, onConfirm) {
    if (confirm(message)) {
        onConfirm();
    }
}

function promptForInput(message, defaultValue = '') {
    return prompt(message, defaultValue);
}

function initConfirmations() {
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', (e) => {
            const message = element.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

function initBorrowBook(buttons) {
    buttons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const borrowerName = promptForInput('Enter borrower name:');
            if (borrowerName && borrowerName.trim()) {
                const href = button.getAttribute('href');
                window.location.href = href + '&borrower=' + encodeURIComponent(borrowerName.trim());
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initConfirmations();
});