document.querySelectorAll('.pagar-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const saldo = parseFloat(this.dataset.saldo);
        const pago = parseFloat(this.dataset.pago);

        if (confirm("¿Marcar pago de $" + pago + " como realizado?")) {
            fetch('../api/deudas-ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=pagar&id=${id}&saldo_actual=${saldo}&pago=${pago}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`saldo-${id}`).textContent = '$' + data.nuevo_saldo.toFixed(2);
                    alert("Pago registrado. Nuevo saldo: $" + data.nuevo_saldo.toFixed(2));
                }
            })
            .catch(err => alert("Error al procesar el pago."));
        }
    });
});