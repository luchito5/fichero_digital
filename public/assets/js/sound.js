(function () {
    'use strict';

    var SOUNDS = {
        entrada: 'entrada.mp3',
        salida: 'salida.mp3',
        logout: 'logout.mp3'
    };

    var BASE = 'assets/sounds/';
    var LOGOUT_MAX_MS = 2500;
    var IDLE_MAX_MS = 10000;
    var LS_MUTE = 'fd_sound_muted';
    var LS_VOL = 'fd_sound_volume';

    var cache = {};
    var activos = new Set();
    var logoutPendiente = false;

    function leer(key, fallback) {
        try {
            var v = localStorage.getItem(key);
            return v === null ? fallback : v;
        } catch (e) { return fallback; }
    }

    function clampVol(v) {
        v = Number(v);
        if (!isFinite(v)) return 100;
        return Math.min(100, Math.max(0, Math.round(v)));
    }

    var estado = {
        muted: leer(LS_MUTE, '0') === '1',
        vol: clampVol(parseInt(leer(LS_VOL, '100'), 10))
    };

    function silencioso() {
        return estado.muted || estado.vol === 0;
    }

    function guardar() {
        try {
            localStorage.setItem(LS_MUTE, estado.muted ? '1' : '0');
            localStorage.setItem(LS_VOL, String(estado.vol));
        } catch (e) { /* almacenamiento no disponible */ }
    }

    function get(name) {
        if (!SOUNDS[name]) return null;
        if (!cache[name]) {
            var audio = new Audio(BASE + SOUNDS[name]);
            audio.preload = 'auto';
            audio.volume = estado.vol / 100;
            cache[name] = audio;
        }
        return cache[name];
    }

    function aplicarVolumen() {
        Object.keys(cache).forEach(function (key) {
            cache[key].volume = estado.vol / 100;
        });
    }

    function reproducir(audio) {
        activos.add(audio);
        var done = function () { activos.delete(audio); };
        audio.addEventListener('ended', done, { once: true });
        audio.addEventListener('error', done, { once: true });
        try {
            audio.currentTime = 0;
            var p = audio.play();
            if (p && typeof p.catch === 'function') p.catch(function () { activos.delete(audio); });
        } catch (e) { activos.delete(audio); }
    }

    function detenerTodos() {
        activos.forEach(function (audio) {
            try { audio.pause(); audio.currentTime = 0; } catch (e) { /* noop */ }
        });
        activos.clear();
    }

    function waitForIdle() {
        if (silencioso() || activos.size === 0) return Promise.resolve(true);
        var inicio = Date.now();
        return new Promise(function (resolve) {
            (function check() {
                if (activos.size === 0) return resolve(true);
                if (Date.now() - inicio >= IDLE_MAX_MS) return resolve(false);
                setTimeout(check, 100);
            })();
        });
    }

    function play(name) {
        if (silencioso()) return Promise.resolve(false);
        var audio = get(name);
        if (!audio) return Promise.resolve(false);
        reproducir(audio);
        return Promise.resolve(true);
    }

    function playAndWait(name, maxMs) {
        if (silencioso()) return Promise.resolve(false);
        var audio = get(name);
        if (!audio) return Promise.resolve(false);

        return new Promise(function (resolve) {
            var resuelto = false;

            function finish(ok) {
                if (resuelto) return;
                resuelto = true;
                audio.removeEventListener('ended', onEnded);
                audio.removeEventListener('error', onError);
                resolve(ok);
            }

            function onEnded() { finish(true); }
            function onError() { finish(false); }

            audio.addEventListener('ended', onEnded);
            audio.addEventListener('error', onError);
            setTimeout(function () { finish(false); }, maxMs);

            reproducir(audio);
        });
    }

    function refrescarUI() {
        var box = document.querySelector('.sound-control');
        var toggle = document.getElementById('soundToggle');
        var slider = document.getElementById('soundVolume');
        if (!box || !toggle) return;
        var off = silencioso();
        box.classList.toggle('is-muted', off);
        toggle.setAttribute('aria-pressed', estado.muted ? 'true' : 'false');
        var label = off ? 'Activar sonidos' : 'Silenciar sonidos';
        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('title', label);
        if (slider) slider.value = String(estado.vol);
    }

    function setMuted(muted) {
        estado.muted = !!muted;
        if (estado.muted) detenerTodos();
        guardar();
        refrescarUI();
    }

    function setVolume(vol) {
        estado.vol = clampVol(vol);
        aplicarVolumen();
        if (estado.vol === 0) detenerTodos();
        guardar();
        refrescarUI();
    }

    function initUI() {
        var box = document.querySelector('.sound-control');
        var toggle = document.getElementById('soundToggle');
        var slider = document.getElementById('soundVolume');
        if (!box || !toggle || !slider) return;

        box.hidden = false;
        refrescarUI();

        toggle.addEventListener('click', function () {
            setMuted(!estado.muted);
        });

        slider.addEventListener('input', function () {
            setVolume(parseInt(slider.value, 10));
        });
    }

    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0) return;
        if (e.ctrlKey || e.shiftKey || e.altKey || e.metaKey) return;
        if (silencioso()) return;
        if (logoutPendiente) { e.preventDefault(); return; }

        var link = e.target && e.target.closest ? e.target.closest('a.logout-link') : null;
        if (!link || link.target === '_blank') return;

        var href = link.getAttribute('href');
        if (!href) return;

        e.preventDefault();
        logoutPendiente = true;
        playAndWait('logout', LOGOUT_MAX_MS).then(function () {
            window.location.href = href;
        });
    });

    function arrancar() {
        Object.keys(SOUNDS).forEach(function (name) { get(name); });
        initUI();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', arrancar);
    } else {
        arrancar();
    }

    window.FDSound = {
        play: play,
        playAndWait: playAndWait,
        setMuted: setMuted,
        setVolume: setVolume,
        waitForIdle: waitForIdle
    };
})();
