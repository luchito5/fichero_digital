document.addEventListener('DOMContentLoaded', () => {
    const entrada = document.getElementById('btnEntrada');
    const salida = document.getElementById('btnSalida');
    const message = document.getElementById('message');
    const overlay = document.getElementById('okOverlay');
    const okTitle = document.getElementById('okTitle');
    const okTime = document.getElementById('okTime');
    const okContinue = document.getElementById('okContinue');

    if (!entrada || !salida) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    const titulos = {
        entrada: 'ENTRADA REGISTRADA CORRECTAMENTE',
        salida: 'SALIDA REGISTRADA CORRECTAMENTE'
    };

    function continuar() {
        window.location.reload();
    }

    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) continuar();
        });
        if (okContinue) okContinue.addEventListener('click', continuar);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !overlay.classList.contains('hidden')) continuar();
        });
    }

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

            message.className = 'alert hidden';
            message.textContent = '';

            if (overlay && okTitle && okTime) {
                okTitle.textContent = titulos[action] || 'REGISTRO CORRECTO';
                okTime.textContent = data.time || '--:--';
                overlay.classList.remove('hidden');
            } else {
                message.className = 'alert success';
                message.textContent = (action === 'entrada' ? 'Entrada' : 'Salida') + ' registrada correctamente: ' + data.time + ' hs';
                setTimeout(() => window.location.reload(), 1000);
            }
        } catch (error) {
            message.className = 'alert error';
            message.textContent = error.message;
            button.disabled = false;
        }
    }

    entrada.addEventListener('click', () => fichar('entrada', entrada));
    salida.addEventListener('click', () => fichar('salida', salida));
});
