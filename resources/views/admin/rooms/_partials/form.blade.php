<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }} <span class="text-danger">*</span></label>
                    <select name="property_id" id="room-property-id" class="form-select js-tom-select" required>
                        <option value="">—</option>
                        @foreach($options['properties'] as $p)
                            <option value="{{ $p->id }}" @selected(old('property_id', $room->property_id) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }} <span class="text-danger">*</span></label>
                    <select name="room_type_id" id="room-room-type-id" class="form-select js-tom-select" required>
                        <option value="">—</option>
                        @foreach($options['room_types'] as $rt)
                            <option value="{{ $rt->id }}" data-property="{{ $rt->property_id }}"
                                @selected(old('room_type_id', $room->room_type_id) == $rt->id)>{{ $rt->name }} <small>({{ $rt->property?->name }})</small></option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="admin.rooms.room_number">{{ __('admin.rooms.room_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="room_number" class="form-control" required value="{{ old('room_number', $room->room_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="admin.rooms.floor">{{ __('admin.rooms.floor') }}</label>
                    <input type="text" name="floor" class="form-control" value="{{ old('floor', $room->floor) }}" placeholder="e.g. 1, 2, G">
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="admin.common.status">{{ __('admin.common.status') }} <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach($options['statuses'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $room->status) === $s)>
                                {{ __('admin.rooms.status.'.$s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-light">{{ __('admin.common.cancel') }}</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> {{ __('admin.common.save') }}</button>
    </div>
</form>
