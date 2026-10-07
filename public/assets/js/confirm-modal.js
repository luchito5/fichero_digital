(function () {
    'use strict';

    const WARNING_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
    const TRASH_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';

    let overlay = null;
    let cancelBtn = null;
    let confirmBtn = null;
    let currentForm = null;
    let lastFocused = null;

    function createModal() {
        overlay = document.createElement('div');
        overlay.className = 'confirm-overlay hidden';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'confirm-title');
        overlay.setAttribute('aria-describedby', 'confirm-desc');

        overlay.innerHTML = `
            <div class="confirm-modal">
                <div class="confirm-icon">${WARNING_ICON}</div>
                <h2 id="confirm-title"></h2>
                <p id="confirm-desc"></p>
                <div class="confirm-actions">
                    <button type="button" class="confirm-btn confirm-btn-cancel">Cancelar</button>
                    <button type="button" class="confirm-btn confirm-btn-confirm">${TRASH_ICON}<span>Sí, eliminar</span></button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        cancelBtn = overlay.querySelector('.confirm-btn-cancel');
        confirmBtn = overlay.querySelector('.confirm-btn-confirm');

        cancelBtn.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', handleConfirm);

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });

        document.addEventListener('keydown', handleKeydown);
    }

    function handleKeydown(e) {
        if (!overlay || overlay.classList.contains('hidden')) return;
        if (e.key === 'Escape') {
            e.preventDefault();
            closeModal();
        }
    }

    function openModal(title, message, form, actionLabel) {
        if (!overlay) createModal();

        currentForm = form;
        lastFocused = document.activeElement;

        overlay.querySelector('#confirm-title').textContent = title;
        overlay.querySelector('#confirm-desc').innerHTML = message;
        overlay.querySelector('.confirm-btn-confirm span').textContent = actionLabel || 'Sí, eliminar';

        overlay.classList.remove('hidden');
        requestAnimationFrame(function () {
            overlay.classList.add('active');
        });

        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            cancelBtn.focus();
        }, 50);
    }

    function closeModal() {
        if (!overlay) return;

        overlay.classList.remove('active');
        document.body.style.overflow = '';

        setTimeout(function () {
            overlay.classList.add('hidden');
            currentForm = null;
            if (lastFocused && lastFocused.focus) lastFocused.focus();
        }, 200);
    }

    function handleConfirm() {
        const form = currentForm;
        if (form && form.hasAttribute('data-confirm-ajax')) {
            // El formulario se resuelve por AJAX: se avisa a la página en lugar de recargarla.
            form.dispatchEvent(new CustomEvent('confirm-ajax', { bubbles: true }));
        } else if (form) {
            form.submit();
        }
        closeModal();
    }

    window.ConfirmDelete = {
        open: openModal
    };

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-confirm-delete]');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();

        const title = trigger.getAttribute('data-confirm-title') || '¿Estás seguro de eliminar este elemento?';
        const message = trigger.getAttribute('data-confirm-message') || 'Esta acción es irreversible y no se puede deshacer.';
        const formId = trigger.getAttribute('data-confirm-form');
        const actionLabel = trigger.getAttribute('data-confirm-action');

        let form = null;
        if (formId) {
            form = document.getElementById(formId);
        } else {
            form = trigger.closest('form');
        }

        if (!form) {
            console.error('ConfirmDelete: no se encontró el formulario asociado');
            return;
        }

        openModal(title, message, form, actionLabel);
    });
})();
