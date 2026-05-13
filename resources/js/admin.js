// Booking ERP — admin JS bundle. Imported by every Blade admin page.
// Wires up SweetAlert2 delete confirmations, Flatpickr, Tom Select,
// Yajra DataTables initializer, and the no-refresh locale switcher.

import 'bootstrap';
import $ from 'jquery';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

window.$ = window.jQuery = $;
window.Swal = Swal;
window.flatpickr = flatpickr;
window.TomSelect = TomSelect;
window.DataTable = DataTable;

const app = window.__APP__ || { locale: 'en', translations: {}, csrfToken: '' };

/* ------------------------------------------------------------------------- */
/* Translation helpers                                                        */
/* ------------------------------------------------------------------------- */

function applyTranslations(dict) {
    document.querySelectorAll('[data-i18n]').forEach((el) => {
        const key = el.getAttribute('data-i18n');
        if (key && dict[key]) {
            el.textContent = dict[key];
        }
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach((el) => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (key && dict[key]) {
            el.setAttribute('placeholder', dict[key]);
        }
    });
    document.querySelectorAll('[data-i18n-title]').forEach((el) => {
        const key = el.getAttribute('data-i18n-title');
        if (key && dict[key]) {
            el.setAttribute('title', dict[key]);
        }
    });
}

window.__t = function (key) {
    return (app.translations && app.translations[key]) || key;
};

/* ------------------------------------------------------------------------- */
/* Locale switcher — POST /lang/switch, swap translations without reload     */
/* ------------------------------------------------------------------------- */

document.addEventListener('click', async (event) => {
    const target = event.target.closest('.js-switch-locale');
    if (!target) return;
    event.preventDefault();
    const locale = target.dataset.locale;
    try {
        const res = await fetch(app.switchLocaleUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': app.csrfToken,
            },
            body: JSON.stringify({ locale }),
        });
        if (!res.ok) throw new Error('switch failed');
        const data = await res.json();
        app.locale = data.locale;
        app.translations = data.translations || {};
        document.documentElement.setAttribute('lang', data.locale);
        document.documentElement.setAttribute('data-locale', data.locale);
        const label = document.getElementById('currentLocaleLabel');
        if (label) label.textContent = data.locale === 'km' ? 'ខ្មែរ' : 'English';
        applyTranslations(app.translations);
    } catch (err) {
        console.error('[locale] switch failed', err);
    }
});

/* ------------------------------------------------------------------------- */
/* SweetAlert2 delete confirmations                                           */
/* ------------------------------------------------------------------------- */

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form.js-delete-form');
    if (!form) return;
    if (form.dataset._confirmed === '1') return;
    event.preventDefault();
    Swal.fire({
        title: window.__t('admin.common.confirm_delete_title'),
        text: window.__t('admin.common.confirm_delete_text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: window.__t('admin.common.confirm_delete_button'),
        cancelButtonText: window.__t('admin.common.cancel'),
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset._confirmed = '1';
            form.submit();
        }
    });
});

/* ------------------------------------------------------------------------- */
/* Flatpickr auto-init                                                        */
/* ------------------------------------------------------------------------- */

function initFlatpickr(root = document) {
    root.querySelectorAll('.js-flatpickr-date').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
    });
    root.querySelectorAll('.js-flatpickr-datetime').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { dateFormat: 'Y-m-d H:i', enableTime: true, allowInput: true });
    });
    root.querySelectorAll('.js-flatpickr-time').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', allowInput: true });
    });
}

/* ------------------------------------------------------------------------- */
/* Tom Select auto-init                                                       */
/* ------------------------------------------------------------------------- */

function initTomSelect(root = document) {
    root.querySelectorAll('.js-tom-select').forEach((el) => {
        if (el.tomselect) return;
        new TomSelect(el, {
            allowEmptyOption: true,
            create: el.dataset.allowCreate === '1',
            plugins: el.multiple ? ['remove_button'] : [],
        });
    });

    root.querySelectorAll('.js-tom-select-remote').forEach((el) => {
        if (el.tomselect) return;
        const url = el.dataset.remoteUrl;
        const valueField = el.dataset.valueField || 'id';
        const labelField = el.dataset.labelField || 'text';
        const searchField = el.dataset.searchField || labelField;
        new TomSelect(el, {
            valueField,
            labelField,
            searchField: [searchField],
            allowEmptyOption: true,
            plugins: el.multiple ? ['remove_button'] : [],
            load: function (query, callback) {
                const u = new URL(url, window.location.origin);
                u.searchParams.set('q', query);
                fetch(u.toString(), {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                }).then((r) => r.json())
                  .then((data) => callback(data.data || data))
                  .catch(() => callback());
            },
        });
    });
}

/* ------------------------------------------------------------------------- */
/* Yajra DataTables initializer                                               */
/* ------------------------------------------------------------------------- */

function initDataTables(root = document) {
    root.querySelectorAll('table.js-datatable').forEach((el) => {
        if (el.dataset._dtInit === '1') return;
        const url = el.dataset.url;
        const columnsAttr = el.dataset.columns;
        if (!url || !columnsAttr) return;
        let columns = [];
        try { columns = JSON.parse(columnsAttr); } catch { columns = []; }
        $(el).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url,
                type: 'GET',
                data: function (d) {
                    d._token = app.csrfToken;
                },
            },
            columns,
            order: el.dataset.defaultOrder ? JSON.parse(el.dataset.defaultOrder) : [[0, 'desc']],
            pageLength: parseInt(el.dataset.pageLength || '15', 10),
            language: {
                emptyTable: window.__t('admin.common.no_records'),
                search: window.__t('admin.common.search') + ':',
                paginate: { previous: '«', next: '»', first: '|«', last: '»|' },
            },
            drawCallback: function () {
                $('.pagination').addClass('pagination-sm');
            },
        });
        el.dataset._dtInit = '1';
    });
}

/* ------------------------------------------------------------------------- */
/* Boot                                                                      */
/* ------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
    initFlatpickr();
    initTomSelect();
    initDataTables();
    applyTranslations(app.translations);
});

// Re-initialize plugins after Bootstrap modal show events (forms inside modals)
document.addEventListener('shown.bs.modal', (e) => {
    initFlatpickr(e.target);
    initTomSelect(e.target);
});

window.bookingErp = { initFlatpickr, initTomSelect, initDataTables, applyTranslations };
