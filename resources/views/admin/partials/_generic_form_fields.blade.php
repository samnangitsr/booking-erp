@php
    /** @var \Illuminate\Database\Eloquent\Model $item */
    $fillable = $item->getFillable();
    $casts = $item->getCasts();
@endphp

<div class="row g-3">
    @foreach($fillable as $field)
        @php
            $type = $casts[$field] ?? 'string';
            $value = old($field, $item->{$field});
            $isDate = str_contains($type, 'date') && ! str_contains($type, 'datetime');
            $isDateTime = str_contains($type, 'datetime') || in_array($field, ['cancelled_at','paid_at','generated_at','login_at','logout_at','last_login_at','calculated_at','used_at']);
            $isTime = in_array($field, ['check_in_time', 'check_out_time']);
            $isBool = $type === 'boolean' || str_starts_with($field, 'is_');
            $isLong = in_array($field, ['description', 'address', 'notes', 'note', 'comment', 'message', 'body']);
            $isFile = in_array($field, ['avatar', 'logo', 'image', 'image_path', 'file_path', 'icon', 'contract_file']);
            $isJson = in_array($type, ['array', 'json']);
        @endphp

        <div class="col-md-{{ $isLong || $isJson ? '12' : '6' }}">
            <label for="{{ $field }}" class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>

            @if($isBool)
                <div class="form-check form-switch">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" class="form-check-input" {{ $value ? 'checked' : '' }}>
                </div>
            @elseif($isLong)
                <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="form-control @error($field) is-invalid @enderror">{{ $value }}</textarea>
            @elseif($isJson)
                <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="form-control font-monospace @error($field) is-invalid @enderror">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</textarea>
            @elseif($isFile)
                <input type="file" id="{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror">
                @if($value)<small class="text-muted d-block mt-1">{{ $value }}</small>@endif
            @elseif($isDate)
                <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" class="form-control js-flatpickr-date @error($field) is-invalid @enderror">
            @elseif($isDateTime)
                <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" class="form-control js-flatpickr-datetime @error($field) is-invalid @enderror">
            @elseif($isTime)
                <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" class="form-control js-flatpickr-time @error($field) is-invalid @enderror">
            @elseif($field === 'status')
                <select id="{{ $field }}" name="{{ $field }}" class="form-select js-tom-select @error($field) is-invalid @enderror">
                    @foreach(['active','inactive','pending','approved','rejected','suspended','blocked','expired','cancelled','paid','unpaid','partial','refunded','confirmed','checked_in','checked_out','no_show'] as $opt)
                        <option value="{{ $opt }}" @selected($value === $opt)>{{ ucfirst(str_replace('_', ' ', $opt)) }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" class="form-control @error($field) is-invalid @enderror">
            @endif
            @error($field)<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
        </div>
    @endforeach
</div>
