document.addEventListener('DOMContentLoaded', () => {
    const campos = document.querySelectorAll('input[data-dni]');
    if (!campos.length) return;

    campos.forEach((campo) => {
        const limpiar = () => {
            const limpio = campo.value.replace(/\D+/g, '').slice(0, 8);
            if (campo.value !== limpio) campo.value = limpio;
        };

        campo.addEventListener('input', limpiar);
        campo.addEventListener('paste', () => setTimeout(limpiar, 0));

        campo.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            const teclasOk = ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
            if (teclasOk.includes(e.key)) return;
            if (/^\d$/.test(e.key)) return;
            e.preventDefault();
        });
    });
});