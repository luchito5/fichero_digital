document.addEventListener('DOMContentLoaded', () => {
    const entrada = document.getElementById('btnEntrada');
    const salida = document.getElementById('btnSalida');
    const message = document.getElementById('message');

    if (!entrada || !salida) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    async function fichar(action, button) {
        button.disabled = true;
        message.className = 'alert';
        message.textContent = 'Procesando...';

        const form = new FormData();
        form.append('action', action);

        try {
            const response = await fetch('api/fichaje.php', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: form
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo completar la operación.');
            }

            message.className = 'alert success';
            message.textContent = action === 'entrada'
                ? `Entrada registrada correctamente: ${data.time} hs`
                : `Salida registrada correctamente: ${data.time} hs`;

            setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            message.className = 'alert error';
            message.textContent = error.message;
            button.disabled = false;
        }
    }

    entrada.addEventListener('click', () => fichar('entrada', entrada));
    salida.addEventListener('click', () => fichar('salida', salida));
});
