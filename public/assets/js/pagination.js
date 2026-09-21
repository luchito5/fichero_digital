(function () {
    'use strict';

    var DEFAULT_PAGE_SIZE = 10;

    function rowsOf(table) {
        var tbody = table.tBodies[0];
        return tbody ? Array.prototype.slice.call(tbody.rows) : [];
    }

    function isItemRow(tr) {
        if (tr.cells.length === 1 && tr.cells[0].hasAttribute('colspan')) return false;
        if ((String(tr.className || '')).indexOf('corregir-row') !== -1) return false;
        return getComputedStyle(tr).display !== 'none';
    }

    function paginate(table) {
        var rows = rowsOf(table);
        if (!rows.length) return;

        var items = rows.filter(isItemRow);

        var sizeAttr = table.getAttribute('data-paginate') || '';
        var size = parseInt(sizeAttr, 10);
        if (!(size > 0)) size = DEFAULT_PAGE_SIZE;

        if (items.length <= size) return;

        var groups = [];
        for (var i = 0; i < items.length; i += size) {
            groups.push(items.slice(i, i + size));
        }

        var current = 0;

        var bar = document.createElement('div');
        bar.className = 'pagination';

        var btnPrev = document.createElement('button');
        btnPrev.type = 'button';
        btnPrev.className = 'page-btn';
        btnPrev.textContent = '‹ Anterior';

        var btnNext = document.createElement('button');
        btnNext.type = 'button';
        btnNext.className = 'page-btn';
        btnNext.textContent = 'Siguiente ›';

        var info = document.createElement('span');
        info.className = 'page-info';

        function render() {
            items.forEach(function (r) { r.style.display = 'none'; });
            (groups[current] || []).forEach(function (r) { r.style.display = ''; });
            info.textContent = 'Página ' + (current + 1) + ' de ' + groups.length;
            btnPrev.disabled = current === 0;
            btnNext.disabled = current === groups.length - 1;
        }

        btnPrev.addEventListener('click', function () {
            if (current === 0) return;
            current--;
            render();
        });

        btnNext.addEventListener('click', function () {
            if (current >= groups.length - 1) return;
            current++;
            render();
        });

        bar.appendChild(btnPrev);
        bar.appendChild(info);
        bar.appendChild(btnNext);

        var wrap = table.parentNode;
        if (wrap) {
            wrap.insertBefore(bar, table.nextSibling);
        }

        render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var tables = document.querySelectorAll('.table-wrap table');
        Array.prototype.forEach.call(tables, paginate);
    });
})();
