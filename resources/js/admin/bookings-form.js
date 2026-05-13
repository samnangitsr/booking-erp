// Bespoke Booking form helpers: items repeater, cascading dropdowns,
// live totals. Loaded by resources/views/admin/bookings/_partials/form.blade.php.

import TomSelect from 'tom-select';

const cfg = window.__bookingForm || {};
const list = document.getElementById('items-list');
const tpl = document.getElementById('item-template');
const addBtn = document.getElementById('btn-add-item');
const propertySelect = document.getElementById('property_id');

let roomTypeOptions = cfg.initialRoomTypes || [];

function fmt(value) {
    const n = Number(value || 0);
    return n.toFixed(2);
}

function escapeHtml(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function ensureRoomTypeOptions(select, selectedId) {
    select.innerHTML =
        '<option value="">—</option>' +
        roomTypeOptions
            .map(
                (rt) =>
                    `<option value="${rt.id}" data-price="${rt.base_price ?? 0}" ${
                        String(rt.id) === String(selectedId) ? 'selected' : ''
                    }>${escapeHtml(rt.text)}</option>`,
            )
            .join('');
}

async function fetchRoomTypes(propertyId) {
    if (!propertyId) {
        roomTypeOptions = [];
        return;
    }
    try {
        const url = new URL(cfg.roomTypesUrl, window.location.origin);
        url.searchParams.set('property_id', propertyId);
        const res = await fetch(url.toString(), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });
        const json = await res.json();
        roomTypeOptions = json.data || [];
    } catch (e) {
        roomTypeOptions = [];
    }
}

async function fetchRatePlans(roomTypeId, ratePlanSelect, selectedId) {
    ratePlanSelect.innerHTML = '<option value="">—</option>';
    if (!roomTypeId) return;
    try {
        const url = new URL(cfg.ratePlansUrl, window.location.origin);
        url.searchParams.set('room_type_id', roomTypeId);
        const res = await fetch(url.toString(), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });
        const json = await res.json();
        const opts = json.data || [];
        ratePlanSelect.innerHTML =
            '<option value="">—</option>' +
            opts
                .map(
                    (rp) =>
                        `<option value="${rp.id}" ${
                            String(rp.id) === String(selectedId) ? 'selected' : ''
                        }>${escapeHtml(rp.text)}</option>`,
                )
                .join('');
    } catch (e) {
        /* ignore */
    }
}

function nightsCount() {
    const ci = document.getElementById('check_in_date')?.value;
    const co = document.getElementById('check_out_date')?.value;
    if (!ci || !co) return 1;
    const start = new Date(ci);
    const end = new Date(co);
    const ms = end.getTime() - start.getTime();
    const days = Math.round(ms / (1000 * 60 * 60 * 24));
    return Math.max(1, days);
}

function recomputeRow(row) {
    const nights = nightsCount();
    const rooms = parseInt(row.querySelector('[data-name="rooms_count"]').value || '0', 10);
    const unit = parseFloat(row.querySelector('[data-name="unit_price"]').value || '0');
    const total = nights * rooms * unit;
    const cell = row.querySelector('.js-item-line-total');
    if (cell) cell.textContent = fmt(total);
    return total;
}

function recomputeAll() {
    let subtotal = 0;
    let rooms = 0;
    list.querySelectorAll('.item-row').forEach((row, idx) => {
        subtotal += recomputeRow(row);
        rooms += parseInt(row.querySelector('[data-name="rooms_count"]').value || '0', 10);
        const badge = row.querySelector('.item-index-badge');
        if (badge) badge.textContent = '#' + (idx + 1);
    });

    const discount = parseFloat(document.getElementById('discount_amount')?.value || '0');
    const tax = parseFloat(document.getElementById('tax_amount')?.value || '0');
    const fee = parseFloat(document.getElementById('fee_amount')?.value || '0');
    const paid = parseFloat(document.getElementById('paid_amount')?.value || '0');
    const grand = Math.max(0, subtotal - discount + tax + fee);
    const due = Math.max(0, grand - paid);

    document.getElementById('summary-nights').textContent = nightsCount();
    document.getElementById('summary-rooms').textContent = rooms;
    document.getElementById('summary-subtotal').textContent = fmt(subtotal);
    document.getElementById('summary-grand').textContent = fmt(grand);
    document.getElementById('summary-due').textContent = fmt(due);
}

function addRow(initial = {}) {
    const fragment = tpl.content.cloneNode(true);
    const row = fragment.querySelector('.item-row');

    // map data-name → form field name with bracket indexing
    const fields = row.querySelectorAll('[data-name]');
    fields.forEach((el) => {
        const name = el.dataset.name;
        const idx = list.children.length; // tentative; reindexed below
        el.setAttribute('name', `items[${idx}][${name}]`);
        if (initial[name] !== undefined && initial[name] !== null) {
            el.value = initial[name];
        }
    });

    list.appendChild(fragment);
    const insertedRow = list.lastElementChild;

    // Populate room type select
    const roomTypeSelect = insertedRow.querySelector('.js-item-room-type');
    ensureRoomTypeOptions(roomTypeSelect, initial.room_type_id);
    const ratePlanSelect = insertedRow.querySelector('.js-item-rate-plan');

    // Wire interactions
    roomTypeSelect.addEventListener('change', async (e) => {
        const rtId = e.target.value;
        const opt = roomTypeOptions.find((o) => String(o.id) === String(rtId));
        if (opt && opt.base_price) {
            const priceInput = insertedRow.querySelector('[data-name="unit_price"]');
            if (!parseFloat(priceInput.value || '0')) {
                priceInput.value = Number(opt.base_price).toFixed(2);
            }
        }
        await fetchRatePlans(rtId, ratePlanSelect, null);
        recomputeAll();
    });

    insertedRow.querySelectorAll('.js-recompute').forEach((el) => {
        el.addEventListener('input', recomputeAll);
    });
    insertedRow.querySelector('.js-remove-item').addEventListener('click', () => {
        if (list.children.length === 1) return;
        insertedRow.remove();
        reindex();
        recomputeAll();
    });

    if (initial.rate_plan_id) {
        fetchRatePlans(initial.room_type_id, ratePlanSelect, initial.rate_plan_id).then(recomputeAll);
    }

    reindex();
    recomputeAll();
    return insertedRow;
}

function reindex() {
    list.querySelectorAll('.item-row').forEach((row, idx) => {
        row.querySelectorAll('[data-name]').forEach((el) => {
            const name = el.dataset.name;
            el.setAttribute('name', `items[${idx}][${name}]`);
        });
    });
}

async function onPropertyChange() {
    const id = propertySelect.value;
    await fetchRoomTypes(id);
    // refresh existing rows
    list.querySelectorAll('.item-row').forEach((row) => {
        const select = row.querySelector('.js-item-room-type');
        const currentVal = select.value;
        ensureRoomTypeOptions(select, currentVal);
    });
    recomputeAll();
}

document.addEventListener('DOMContentLoaded', () => {
    // Seed rows
    const initial = Array.isArray(cfg.initialItems) ? cfg.initialItems : [];
    if (initial.length === 0) addRow();
    initial.forEach((it) => addRow(it));

    // Date change triggers recompute
    ['check_in_date', 'check_out_date', 'discount_amount', 'tax_amount', 'fee_amount', 'paid_amount'].forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', recomputeAll);
        el.addEventListener('input', recomputeAll);
    });

    addBtn?.addEventListener('click', () => addRow());

    if (propertySelect) {
        propertySelect.addEventListener('change', onPropertyChange);
    }

    recomputeAll();
});
