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

    function recargar() {
        const espera = window.FDSound?.waitForIdle?.();
        if (espera && typeof espera.then === 'function') {
            espera.then(() => window.location.reload());
        } else {
            window.location.reload();
        }
    }

    function continuar() {
        recargar();
    }

    function formatearTiempo(total) {
        const min = Math.floor(total / 60);
        const seg = total % 60;
        return min + ':' + String(seg).padStart(2, '0');
    }

    let timerBloqueo = null;

    function bloquearFichaje(segundos) {
        let restante = Math.max(1, Number(segundos) || 0);
        clearInterval(timerBloqueo);

        entrada.disabled = true;
        salida.disabled = true;
        message.className = 'alert error';
        message.textContent = 'Fichaje bloqueado por seguridad. Podés volver a fichar en ' + formatearTiempo(restante) + '.';

        timerBloqueo = setInterval(() => {
            restante -= 1;
            if (restante <= 0) {
                clearInterval(timerBloqueo);
                window.location.reload();
                return;
            }
            message.textContent = 'Fichaje bloqueado por seguridad. Podés volver a fichar en ' + formatearTiempo(restante) + '.';
        }, 1000);
    }

const bloqueoInicial = parseInt(message?.getAttribute('data-retry-after') || '0', 10);
if (bloqueoInicial > 0) bloquearFichaje(bloqueoInicial);

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
                if (data && data.cooldown && data.retry_after > 0) {
                    bloquearFichaje(data.retry_after);
                    return;
                }
                throw new Error(data.message || 'No se pudo completar la operación.');
            }

            message.className = 'alert hidden';
            message.textContent = '';

            if (overlay && okTitle && okTime) {
                okTitle.textContent = titulos[action] || 'REGISTRO CORRECTO';
                okTime.textContent = data.time || '--:--';
                overlay.classList.remove('hidden');
                window.FDSound?.play(action);
            } else {
                message.className = 'alert success';
                message.textContent = (action === 'entrada' ? 'Entrada' : 'Salida') + ' registrada correctamente: ' + data.time + ' hs';
                window.FDSound?.play(action);
                setTimeout(recargar, 1000);
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
