@php
    /** @var \App\Models\Role $item */
    /** @var array $options */
    $permissions = $options['permissions'] ?? collect();
    $assigned = collect($options['assignedPermissions'] ?? [])->map(fn ($id) => (int) $id)->all();
    $assignedSet = array_flip($assigned);
    $grouped = $permissions->groupBy('module');
    $totalCount = $permissions->count();
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">
            <span data-i18n="admin.common.name">{{ __('admin.common.name') }}</span> <span class="text-danger">*</span>
        </label>
        <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
    </div>

    <div class="col-md-6">
        <label for="guard_name" class="form-label" data-i18n="admin.roles.guard_name">{{ __('admin.roles.guard_name') }}</label>
        <input type="text" id="guard_name" name="guard_name" value="{{ old('guard_name', $item->guard_name ?: 'web') }}"
               class="form-control @error('guard_name') is-invalid @enderror">
        @error('guard_name')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label" data-i18n="admin.common.description">{{ __('admin.common.description') }}</label>
        <textarea id="description" name="description" rows="2"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description) }}</textarea>
        @error('description')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mt-4">
    <div class="d-flex flex-wrap align-items-end justify-content-between mb-2 gap-2">
        <div>
            <label class="form-label mb-0 fw-semibold">
                <span data-i18n="admin.roles.permissions">{{ __('admin.roles.permissions') }}</span>
                <span class="text-danger">*</span>
            </label>
            <div class="text-muted small">
                <span data-i18n="admin.roles.permissions_hint">{{ __('admin.roles.permissions_hint') }}</span>
            </div>
        </div>
        <div class="text-muted small">
            <span data-i18n="admin.roles.selected">{{ __('admin.roles.selected') }}</span>:
            <span id="js-permissions-count" class="fw-semibold">0</span> / {{ $totalCount }}
        </div>
    </div>

    @error('permissions')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror

    <div class="table-responsive border rounded">
        <table class="table table-bordered align-middle mb-0 js-role-permissions-table">
            <thead class="bg-light">
                <tr>
                    <th style="width: 28%;" class="d-flex align-items-center justify-content-between gap-2">
                        <span data-i18n="admin.roles.group">{{ __('admin.roles.group') }}</span>
                        <div class="form-check m-0">
                            <input type="checkbox" id="js-perm-master-all" class="form-check-input js-perm-master-all"
                                   aria-label="{{ __('admin.roles.select_all') }}">
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check m-0">
                                <input type="checkbox" class="form-check-input js-perm-master-all"
                                       aria-label="{{ __('admin.roles.select_all') }}">
                            </div>
                            <span data-i18n="admin.roles.access">{{ __('admin.roles.access') }}</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $module => $items)
                    @php
                        $moduleKey = (string) $module;
                        $moduleIds = $items->pluck('id')->map(fn ($id) => (int) $id)->all();
                        $moduleAssignedCount = collect($moduleIds)->filter(fn ($id) => isset($assignedSet[$id]))->count();
                        $moduleAllChecked = $moduleAssignedCount > 0 && $moduleAssignedCount === count($moduleIds);
                        $hasNavLabel = trans()->has('admin.nav.'.$moduleKey);
                        $moduleLabel = $hasNavLabel ? __('admin.nav.'.$moduleKey) : ucwords(str_replace('_', ' ', $moduleKey));
                    @endphp
                    <tr data-permission-group="{{ $moduleKey }}">
                        <td>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $moduleLabel }}</div>
                                    <div class="text-muted small text-uppercase">{{ $moduleKey }}</div>
                                </div>
                                <div class="form-check m-0">
                                    <input type="checkbox" class="form-check-input js-perm-group-master"
                                           data-group="{{ $moduleKey }}"
                                           aria-label="{{ __('admin.roles.select_group', ['group' => $moduleLabel]) }}"
                                           @checked($moduleAllChecked)>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($items as $perm)
                                    @php
                                        $checked = isset($assignedSet[(int) $perm->id]);
                                        $actionKey = str_replace($moduleKey.'.', '', $perm->name);
                                        $actionTransKey = 'admin.roles.actions.'.$actionKey;
                                        $actionLabel = trans()->has($actionTransKey) ? __($actionTransKey) : ucwords(str_replace('_', ' ', $actionKey));
                                    @endphp
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input js-perm-checkbox"
                                               id="permission-{{ $perm->id }}"
                                               name="permissions[]"
                                               value="{{ $perm->id }}"
                                               data-group="{{ $moduleKey }}"
                                               @checked(in_array((int) $perm->id, old('permissions', $assigned)))>
                                        <label for="permission-{{ $perm->id }}" class="form-check-label">
                                            <code class="small text-body">{{ $perm->name }}</code>
                                            <span class="text-muted small">({{ $actionLabel }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const root = document.querySelector('.js-role-permissions-table');
    if (!root) return;

    const allCheckboxes = () => Array.from(root.querySelectorAll('.js-perm-checkbox'));
    const groupMasters = () => Array.from(root.querySelectorAll('.js-perm-group-master'));
    const globalMasters = () => Array.from(document.querySelectorAll('.js-perm-master-all'));
    const counter = document.getElementById('js-permissions-count');

    function refreshCounters() {
        const all = allCheckboxes();
        const checked = all.filter(cb => cb.checked).length;
        if (counter) counter.textContent = String(checked);

        groupMasters().forEach(master => {
            const group = master.dataset.group;
            const groupBoxes = all.filter(cb => cb.dataset.group === group);
            const groupChecked = groupBoxes.filter(cb => cb.checked).length;
            master.checked = groupChecked === groupBoxes.length && groupBoxes.length > 0;
            master.indeterminate = groupChecked > 0 && groupChecked < groupBoxes.length;
        });

        const totalChecked = all.filter(cb => cb.checked).length;
        globalMasters().forEach(master => {
            master.checked = totalChecked === all.length && all.length > 0;
            master.indeterminate = totalChecked > 0 && totalChecked < all.length;
        });
    }

    globalMasters().forEach(master => {
        master.addEventListener('change', (e) => {
            const target = e.currentTarget.checked;
            allCheckboxes().forEach(cb => { cb.checked = target; });
            refreshCounters();
        });
    });

    groupMasters().forEach(master => {
        master.addEventListener('change', (e) => {
            const group = e.currentTarget.dataset.group;
            const target = e.currentTarget.checked;
            allCheckboxes()
                .filter(cb => cb.dataset.group === group)
                .forEach(cb => { cb.checked = target; });
            refreshCounters();
        });
    });

    allCheckboxes().forEach(cb => {
        cb.addEventListener('change', refreshCounters);
    });

    refreshCounters();
})();
</script>
@endpush
