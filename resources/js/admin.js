// Booking ERP — admin JS bundle. Imported by every Blade admin page.
// Wires up SweetAlert2 delete confirmations, Flatpickr, Tom Select,
// Yajra DataTables initializer, and the no-refresh locale switcher.

import * as bootstrap from 'bootstrap';
import $ from 'jquery';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

window.bootstrap = bootstrap;
window.$ = window.jQuery = $;
window.Swal = Swal;
window.flatpickr = flatpickr;
window.TomSelect = TomSelect;
window.DataTable = DataTable;

const app = window.__APP__ || { locale: 'en', translations: {}, csrfToken: '' };
const themeStorageKey = 'bookingErp.adminTheme';
const themeClasses = ['light-theme', 'dark-theme', 'semi-dark', 'minimal-theme'];
const desktopSidebarMedia = window.matchMedia('(min-width: 1025px)');

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

function getDirectSubmenu(listItem) {
    return Array.from(listItem.children).find((child) => child.tagName === 'UL') || null;
}

function getDirectMenuTrigger(listItem) {
    return Array.from(listItem.children).find((child) => child.matches?.('a.has-arrow')) || null;
}

function setMenuExpanded(listItem, expanded) {
    const trigger = getDirectMenuTrigger(listItem);
    const submenu = getDirectSubmenu(listItem);

    if (trigger) {
        trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    if (submenu) {
        submenu.classList.add('mm-collapse');
        submenu.classList.toggle('mm-show', expanded);
    }

    listItem.classList.toggle('mm-active', expanded);
}

function collapseMenuBranch(listItem) {
    setMenuExpanded(listItem, false);

    listItem.querySelectorAll('li.mm-active').forEach((nestedItem) => {
        nestedItem.classList.remove('mm-active');
    });

    listItem.querySelectorAll('ul.mm-show').forEach((submenu) => {
        submenu.classList.remove('mm-show');
    });

    listItem.querySelectorAll('a.has-arrow[aria-expanded="true"]').forEach((trigger) => {
        trigger.setAttribute('aria-expanded', 'false');
    });
}

function closeSiblingMenuBranches(listItem) {
    const parentList = listItem.parentElement;
    if (!parentList) return;

    Array.from(parentList.children).forEach((sibling) => {
        if (sibling !== listItem && sibling instanceof HTMLElement) {
            collapseMenuBranch(sibling);
        }
    });
}

function initSidebarMenu() {
    const menu = document.getElementById('menu');
    if (!menu) return;

    menu.querySelectorAll('li > ul').forEach((submenu) => {
        submenu.classList.add('mm-collapse');
    });

    const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    const currentLink = Array.from(menu.querySelectorAll('a[href]')).find((link) => {
        const href = link.getAttribute('href') || '';
        if (!href || href.startsWith('javascript:') || href === '#') return false;

        const url = new URL(link.href, window.location.origin);
        return url.origin === window.location.origin && (url.pathname.replace(/\/+$/, '') || '/') === currentPath;
    });

    if (currentLink) {
        let currentItem = currentLink.closest('li');

        while (currentItem && menu.contains(currentItem)) {
            setMenuExpanded(currentItem, true);

            const parentList = currentItem.parentElement;
            if (!parentList || parentList === menu) break;

            parentList.classList.add('mm-collapse', 'mm-show');
            currentItem = parentList.closest('li');
        }
    }

    menu.querySelectorAll('a.has-arrow').forEach((trigger) => {
        const listItem = trigger.parentElement;
        if (!(listItem instanceof HTMLElement)) return;

        const submenu = getDirectSubmenu(listItem);
        if (!submenu) return;

        trigger.setAttribute('aria-expanded', submenu.classList.contains('mm-show') ? 'true' : 'false');

        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const wrapper = document.querySelector('.wrapper');
            const collapsedDesktop = wrapper?.classList.contains('toggled')
                && desktopSidebarMedia.matches
                && !wrapper.classList.contains('sidebar-hovered');

            if (collapsedDesktop) return;

            const willExpand = !submenu.classList.contains('mm-show');

            if (willExpand) {
                closeSiblingMenuBranches(listItem);
            }

            setMenuExpanded(listItem, willExpand);
        });
    });
}

function initSidebarChrome() {
    const wrapper = document.querySelector('.wrapper');
    if (!wrapper) return;

    const sidebar = wrapper.querySelector('.sidebar-wrapper');
    const desktopToggle = wrapper.querySelector('.toggle-icon');
    const mobileToggle = wrapper.querySelector('.mobile-toggle-icon');
    const overlay = wrapper.querySelector('.overlay.nav-toggle-icon');

    const syncSidebarHover = (hovered) => {
        if (!desktopSidebarMedia.matches || !wrapper.classList.contains('toggled')) {
            wrapper.classList.remove('sidebar-hovered');
            return;
        }

        wrapper.classList.toggle('sidebar-hovered', hovered);
    };

    desktopToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        wrapper.classList.toggle('toggled');

        if (!wrapper.classList.contains('toggled')) {
            wrapper.classList.remove('sidebar-hovered');
        }
    });

    mobileToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        wrapper.classList.add('toggled');
    });

    overlay?.addEventListener('click', () => {
        wrapper.classList.remove('toggled', 'sidebar-hovered');
    });

    sidebar?.addEventListener('mouseenter', () => syncSidebarHover(true));
    sidebar?.addEventListener('mouseleave', () => syncSidebarHover(false));

    window.addEventListener('resize', () => {
        if (!desktopSidebarMedia.matches) {
            wrapper.classList.remove('sidebar-hovered');
        }
    });
}

function initHeaderSearch() {
    const searchForm = document.querySelector('.top-header .navbar .searchbar');
    if (!searchForm) return;

    document.querySelector('.search-toggle-icon')?.addEventListener('click', () => {
        searchForm.classList.add('full-searchbar');
    });

    searchForm.querySelector('.search-close-icon')?.addEventListener('click', () => {
        searchForm.classList.remove('full-searchbar');
    });
}

function syncThemeControls(theme) {
    const controlMap = {
        'light-theme': 'LightTheme',
        'dark-theme': 'DarkTheme',
        'semi-dark': 'SemiDarkTheme',
    };

    Object.values(controlMap).forEach((id) => {
        const input = document.getElementById(id);
        if (input instanceof HTMLInputElement) {
            input.checked = controlMap[theme] === id;
        }
    });
}

function setAdminTheme(theme) {
    const root = document.documentElement;

    themeClasses.forEach((className) => {
        root.classList.remove(className);
    });

    root.classList.add(theme);
    localStorage.setItem(themeStorageKey, theme);
    syncThemeControls(theme);
}

function initThemeSwitcher() {
    const root = document.documentElement;
    const currentTheme = themeClasses.find((className) => root.classList.contains(className)) || 'light-theme';
    const storedTheme = localStorage.getItem(themeStorageKey);

    syncThemeControls(storedTheme || currentTheme);

    if (storedTheme && storedTheme !== currentTheme) {
        setAdminTheme(storedTheme);
    }

    const radioMap = {
        LightTheme: 'light-theme',
        DarkTheme: 'dark-theme',
        SemiDarkTheme: 'semi-dark',
    };

    Object.entries(radioMap).forEach(([id, theme]) => {
        document.getElementById(id)?.addEventListener('change', (event) => {
            if (event.target instanceof HTMLInputElement && event.target.checked) {
                setAdminTheme(theme);
            }
        });
    });
}

function initBackToTop() {
    const trigger = document.querySelector('.back-to-top');
    if (!trigger) return;

    const syncVisibility = () => {
        trigger.classList.toggle('d-block', window.scrollY > 300);
        trigger.classList.toggle('d-none', window.scrollY <= 300);
    };

    syncVisibility();
    window.addEventListener('scroll', syncVisibility, { passive: true });

    trigger.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ------------------------------------------------------------------------- */
/* Boot                                                                      */
/* ------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
    initSidebarChrome();
    initSidebarMenu();
    initHeaderSearch();
    initThemeSwitcher();
    initBackToTop();
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

window.bookingErp = {
    initFlatpickr,
    initTomSelect,
    initDataTables,
    applyTranslations,
    initSidebarChrome,
    initSidebarMenu,
    initHeaderSearch,
    initThemeSwitcher,
};
