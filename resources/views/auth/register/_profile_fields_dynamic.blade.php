{{-- Dynamic profile fields for registration based on role_columns --}}
{{-- Usage: @include('auth.register._profile_fields_dynamic', ['role' => 'umum', 'rolesData' => $rolesData]) --}}

@php
    $roleData = $rolesData[$role] ?? null;
    if (!$roleData) return;

    $profileColumns = $roleData['columns'];
    $enumOptions = $roleData['enumOptions'];

    $textFields = [];
    $dateFields = [];
    $enumFields = [];
    $fileFields = [];
    $textareaFields = [];
    $numberFields = [];

    $skipFields = ['user_id', 'id', 'created_at', 'updated_at'];

    foreach ($profileColumns as $col) {
        if (in_array($col->column_name, $skipFields)) continue;

        $field = $col->column_name;

        if (in_array($field, ['name', 'email', 'username', 'password', 'photo'])) continue;

        if ($field === 'kartu_identitas' || $col->column_type === 'blob') {
            $fileFields[] = $col;
        } elseif (in_array($col->column_type, ['enum', 'set'])) {
            $enumFields[] = $col;
        } elseif (in_array($col->column_type, ['text', 'longtext', 'mediumtext'])) {
            $textareaFields[] = $col;
        } elseif (in_array($col->column_type, ['date', 'datetime', 'timestamp'])) {
            $dateFields[] = $col;
        } elseif (in_array($col->column_type, ['int', 'bigint', 'smallint', 'tinyint', 'decimal', 'float', 'double'])) {
            $numberFields[] = $col;
        } else {
            $textFields[] = $col;
        }
    }
@endphp

{{-- Text fields --}}
@foreach($textFields as $col)
    @php
        $value = old($col->column_name);
        $label = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
        $placeholder = isset($colPlaceholder) ? $colPlaceholder($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
    @endphp
    <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}">{{ $label }}</label>
    <input type="text"
        name="{{ $col->column_name }}"
        id="{{ $col->column_name }}"
        class="login-input"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        maxlength="{{ $col->column_length ?? 255 }}"
        {{ !$col->is_nullable ? 'required' : '' }}>
@endforeach

{{-- Date fields (tempat & tanggal lahir) --}}
@foreach($dateFields as $col)
    @php $value = old($col->column_name); @endphp
    @if($col->column_name === 'tanggal_lahir')
        @php $labelDate = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name)); @endphp
        <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}">{{ $labelDate }}</label>
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 15px;">
            @foreach($dateFields as $dcol)
                @if($dcol->column_name === 'tempat_lahir')
                    @php
                        $tValue = old($dcol->column_name);
                        $labelTempat = isset($colLabel) ? $colLabel($dcol->column_name) : ($dcol->column_label ?? str()->headline($dcol->column_name));
                        $placeholderTempat = isset($colPlaceholder) ? $colPlaceholder($dcol->column_name) : ($dcol->column_label ?? str()->headline($dcol->column_name));
                    @endphp
                    <input type="text"
                        name="{{ $dcol->column_name }}"
                        id="{{ $dcol->column_name }}"
                        class="login-input"
                        placeholder="{{ $placeholderTempat }}"
                        value="{{ $tValue }}"
                        {{ !$dcol->is_nullable ? 'required' : '' }}>
                @endif
            @endforeach
            <input type="date"
                name="{{ $col->column_name }}"
                id="{{ $col->column_name }}"
                class="login-input"
                value="{{ $value }}"
                {{ !$col->is_nullable ? 'required' : '' }}>
        </div>
    @endif
@endforeach

{{-- Enum fields --}}
@foreach($enumFields as $col)
    @php
        $options = $enumOptions[$col->column_name] ?? ($col->options ?? []);
        $value = old($col->column_name);
        $isGender = $col->column_name === 'jenis_kelamin';
        $label = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
    @endphp

    @if($isGender)
        <div style="grid-column: 1 / -1;">
            <label class="{{ !$col->is_nullable ? 'required' : '' }}">{{ $label }}</label>
            <div class="radio-group">
                @foreach($options as $option)
                    <label class="radio-item">
                        <input type="radio" name="{{ $col->column_name }}" value="{{ $option }}"
                            {{ $value == $option ? 'checked' : '' }}
                            {{ !$col->is_nullable ? 'required' : '' }}> {{ isset($colEnumOption) ? $colEnumOption($option, $col->column_name) : $option }}
                    </label>
                @endforeach
            </div>
        </div>
    @else
        <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}">{{ $label }}</label>
        <select name="{{ $col->column_name }}" id="{{ $col->column_name }}" class="login-input" {{ !$col->is_nullable ? 'required' : '' }}>
            <option value="">-- {{ __('auth.select_option') }} --</option>
            @foreach($options as $option)
                <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>{{ isset($colEnumOption) ? $colEnumOption($option, $col->column_name) : $option }}</option>
            @endforeach
        </select>
    @endif
@endforeach

{{-- Number fields --}}
@foreach($numberFields as $col)
    @php
        $value = old($col->column_name);
        $label = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
        $placeholder = isset($colPlaceholder) ? $colPlaceholder($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
    @endphp
    <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}">{{ $label }}</label>
    <input type="number"
        name="{{ $col->column_name }}"
        id="{{ $col->column_name }}"
        class="login-input"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        {{ !$col->is_nullable ? 'required' : '' }}>
@endforeach

{{-- File fields (spans full grid width) --}}
@foreach($fileFields as $col)
    @php $label = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name)); @endphp
    <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}" id="label-{{ $col->column_name }}" style="grid-column: 1;">{{ $label }}</label>
    <div class="file-upload-wrapper" style="grid-column: 2;">
        <button type="button" class="file-upload-btn"
            onclick="document.getElementById('{{ $col->column_name }}').click()">{{ __('auth.choose_file') }}</button>
        <span class="file-upload-text" id="file-name-{{ $col->column_name }}">{{ __('auth.no_file') }}</span>
        <input type="file"
            name="{{ $col->column_name }}"
            id="{{ $col->column_name }}"
            accept=".jpg,.jpeg,.png,.pdf"
            onchange="var el=document.getElementById('file-name-{{ $col->column_name }}');if(el)el.textContent=this.files[0]?this.files[0].name:'{{ __('auth.no_file') }}';"
            {{ !$col->is_nullable ? 'required' : '' }}>
    </div>
@endforeach

{{-- Textarea fields --}}
@foreach($textareaFields as $col)
    @php
        $value = old($col->column_name);
        $label = isset($colLabel) ? $colLabel($col->column_name) : ($col->column_label ?? str()->headline($col->column_name));
    @endphp
    <label for="{{ $col->column_name }}" class="{{ !$col->is_nullable ? 'required' : '' }}" style="grid-column: 1 / -1;">{{ $label }}</label>
    <textarea
        name="{{ $col->column_name }}"
        id="{{ $col->column_name }}"
        class="login-input"
        rows="4"
        style="resize: vertical; min-height: 100px;"
        {{ !$col->is_nullable ? 'required' : '' }}>{{ $value }}</textarea>
@endforeach
