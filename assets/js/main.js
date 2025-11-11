// Scripts JavaScript customizados

// Auto-hide alerts após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Focar no campo de QR Code quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    const qrInput = document.getElementById('codigo_qr');
    if (qrInput) {
        qrInput.focus();
    }
});

// Função para imprimir QR Code
function imprimirQRCode() {
    window.print();
}

// Função para copiar código QR
function copiarCodigoQR(codigo) {
    navigator.clipboard.writeText(codigo).then(function() {
        alert('Código copiado para a área de transferência!');
    });
}

