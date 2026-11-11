/**
 * SIMBIM UI Helpers: Select2 Initialization & 6-Per-Page Client Table Pagination.
 */
(function (window, document) {
    'use strict';

    const clientTables = new Map();

    function initSelect2(root) {
        if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') {
            return;
        }

        const $ = window.jQuery;
        const scope = root || document;
        const selects = scope.querySelectorAll('select[data-control="select2"], select[data-kt-select2="true"]');

        selects.forEach(function (el) {
            const $el = $(el);
            if (el.dataset.simbimSelect2Ready === '1') {
                return;
            }

            if ($el.data('select2')) {
                try {
                    $el.select2('destroy');
                } catch (e) {
                    // Ignore already-detached select2 instance
                }
            }

            const options = {
                width: '100%',
                minimumResultsForSearch: el.getAttribute('data-hide-search') === 'true' ? Infinity : 0,
            };

            const dir = document.body?.getAttribute('direction');
            if (dir) {
                options.dir = dir;
            }

            const placeholder = el.getAttribute('data-placeholder');
            if (placeholder) {
                options.placeholder = placeholder;
            }

            if (el.getAttribute('data-allow-clear') === 'true') {
                options.allowClear = true;
            }

            const parentSelector = el.getAttribute('data-dropdown-parent');
            if (parentSelector) {
                const parentEl = document.querySelector(parentSelector);
                if (parentEl) {
                    options.dropdownParent = $(parentEl);
                }
            } else {
                const closestModal = el.closest('.modal');
                if (closestModal) {
                    options.dropdownParent = $(closestModal);
                }
            }

            $el.select2(options);
            el.dataset.simbimSelect2Ready = '1';

            // Bridge Select2 UI changes so native DOM `change` listeners also react cleanly
            $el.off('.simbimSelect2').on('select2:select.simbimSelect2 select2:clear.simbimSelect2', function () {
                if (el.dataset.simbimDispatching === '1') {
                    return;
                }
                el.dataset.simbimDispatching = '1';
                try {
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                } finally {
                    el.dataset.simbimDispatching = '0';
                }
            });
        });

        if (!window.__simbimSelect2FocusBound) {
            window.__simbimSelect2FocusBound = true;
            $(document).on('select2:open', function () {
                const searchFields = document.querySelectorAll('.select2-container--open .select2-search__field');
                if (searchFields.length > 0) {
                    searchFields[searchFields.length - 1].focus();
                }
            });
        }
    }

    function syncSelect2Value(elOrSelector, value) {
        const el = typeof elOrSelector === 'string'
            ? document.querySelector(elOrSelector)
            : elOrSelector;

        if (!el) {
            return;
        }

        el.value = value !== undefined && value !== null ? value : '';
        if (typeof window.jQuery !== 'undefined' && window.jQuery(el).data('select2')) {
            window.jQuery(el).trigger('change.select2');
        }
    }

    function buildPaginationButton(label, targetPage, disabled, active, onClick) {
        const li = document.createElement('li');
        li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'page-link';
        btn.innerHTML = label;
        btn.disabled = Boolean(disabled);
        if (!disabled && !active) {
            btn.addEventListener('click', function () {
                onClick(targetPage);
            });
        }

        li.appendChild(btn);
        return li;
    }

    function setupTablePagination(table) {
        const perPage = Math.max(1, parseInt(table.getAttribute('data-simbim-paginate') || '6', 10) || 6);
        const rowSelector = table.getAttribute('data-row-selector') || 'tbody tr:not([data-simbim-empty])';
        const pagerId = table.getAttribute('data-pager-id');
        const infoId = table.getAttribute('data-info-id');
        const counterId = table.getAttribute('data-counter-id');
        const counterSuffix = table.getAttribute('data-counter-suffix') || 'Sesi';

        const state = {
            table: table,
            perPage: perPage,
            rowSelector: rowSelector,
            pagerId: pagerId,
            infoId: infoId,
            counterId: counterId,
            counterSuffix: counterSuffix,
            currentPage: 1,
        };

        clientTables.set(table.id, state);
        renderTablePage(table.id, 1);
    }

    function renderTablePage(tableId, page) {
        const state = clientTables.get(tableId);
        if (!state) {
            return;
        }

        const allRows = Array.from(state.table.querySelectorAll(state.rowSelector));
        const matchedRows = allRows.filter(function (row) {
            return row.getAttribute('data-simbim-filtered') !== '0';
        });

        const emptyRow = state.table.querySelector('tbody tr[data-simbim-empty="1"]');
        const total = matchedRows.length;
        const totalPages = Math.max(1, Math.ceil(total / state.perPage));
        state.currentPage = Math.min(Math.max(1, page || state.currentPage || 1), totalPages);

        const startIdx = (state.currentPage - 1) * state.perPage;
        const endIdx = startIdx + state.perPage;

        allRows.forEach(function (row) {
            row.style.display = 'none';
        });

        matchedRows.forEach(function (row, idx) {
            row.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
        });

        if (emptyRow) {
            emptyRow.style.display = total === 0 ? '' : 'none';
        }

        if (state.counterId) {
            const counterEl = document.getElementById(state.counterId);
            if (counterEl) {
                counterEl.textContent = 'Menampilkan ' + total + ' ' + state.counterSuffix;
            }
        }

        if (state.infoId) {
            const infoEl = document.getElementById(state.infoId);
            if (infoEl) {
                if (total === 0) {
                    infoEl.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    const first = startIdx + 1;
                    const last = Math.min(endIdx, total);
                    infoEl.textContent = 'Menampilkan ' + first + ' sampai ' + last + ' dari ' + total + ' data';
                }
            }
        }

        if (state.pagerId) {
            const pagerEl = document.getElementById(state.pagerId);
            if (pagerEl) {
                pagerEl.innerHTML = '';
                if (total > state.perPage) {
                    const ul = document.createElement('ul');
                    ul.className = 'pagination pagination-sm mb-0';

                    ul.appendChild(buildPaginationButton('&laquo;', state.currentPage - 1, state.currentPage <= 1, false, function (p) {
                        renderTablePage(tableId, p);
                    }));

                    for (let p = 1; p <= totalPages; p++) {
                        ul.appendChild(buildPaginationButton(String(p), p, false, p === state.currentPage, function (target) {
                            renderTablePage(tableId, target);
                        }));
                    }

                    ul.appendChild(buildPaginationButton('&raquo;', state.currentPage + 1, state.currentPage >= totalPages, false, function (p) {
                        renderTablePage(tableId, p);
                    }));

                    pagerEl.appendChild(ul);
                }
            }
        }
    }

    function initClientTables() {
        document.querySelectorAll('table[data-simbim-paginate]').forEach(function (table) {
            if (!table.id) {
                table.id = 'simbim_tbl_' + Math.random().toString(36).slice(2, 9);
            }
            setupTablePagination(table);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSelect2(document);
        initClientTables();
    });

    window.SimbimUI = {
        initSelect2: initSelect2,
        syncSelect2Value: syncSelect2Value,
        refreshPagination: function (tableId, resetPage) {
            renderTablePage(tableId, resetPage ? 1 : undefined);
        },
    };
})(window, document);

