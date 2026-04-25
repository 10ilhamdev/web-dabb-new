{{-- Profile fields for instansi_swasta (tanpa jenis_kelamin) --}}

{{-- Tempat Lahir --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_tempat_lahir') }}</label>
    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile?->tempat_lahir) }}"
        placeholder="{{ __('cms.pengguna.form_tempat_lahir_placeholder') }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('tempat_lahir')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Tanggal Lahir --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_tanggal_lahir') }}</label>
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile?->tanggal_lahir) }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('tanggal_lahir')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Kartu Identitas File --}}
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_kartu_identitas') }}</label>
    <input type="file" name="kartu_identitas_file" accept=".jpg,.jpeg,.png,.pdf"
        class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-[#174E93] hover:file:bg-blue-100 cursor-pointer">
    <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.form_kartu_identitas_help') }}</p>
    @if ($profile?->kartu_identitas)
        <p class="text-xs text-gray-600 mt-1">
            {{ __('cms.pengguna.form_kartu_identitas_current') }}:
            <a href="{{ asset('storage/' . $profile->kartu_identitas) }}" target="_blank"
                class="text-blue-600 hover:underline">{{ __('cms.pengguna.form_kartu_identitas_view') }}</a>
        </p>
    @endif
    @error('kartu_identitas_file')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Nomor Kartu Identitas --}}
<div>
    <label
        class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_nomor_kartu_identitas') }}</label>
    <input type="text" name="nomor_kartu_identitas"
        value="{{ old('nomor_kartu_identitas', $profile?->nomor_kartu_identitas) }}"
        placeholder="{{ __('cms.pengguna.form_nomor_kartu_identitas_placeholder') }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('nomor_kartu_identitas')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Alamat --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_alamat') }}</label>
    <textarea name="alamat" rows="3" placeholder="{{ __('cms.pengguna.form_alamat_placeholder') }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('alamat', $profile?->alamat) }}</textarea>
    @error('alamat')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Nomor WhatsApp --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_nomor_whatsapp') }}</label>
    <input type="text" name="nomor_whatsapp" value="{{ old('nomor_whatsapp', $profile?->nomor_whatsapp) }}"
        placeholder="{{ __('cms.pengguna.form_nomor_whatsapp_placeholder') }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('nomor_whatsapp')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Jenis Keperluan --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_jenis_keperluan') }}</label>
    <select name="jenis_keperluan"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
        <option value="">{{ __('cms.pengguna.form_jenis_keperluan_placeholder') }}</option>
        @foreach ($jenisKeperluanOptions as $value => $label)
            <option value="{{ $value }}"
                {{ old('jenis_keperluan', $profile?->jenis_keperluan) === $value ? 'selected' : '' }}>
                {{ $label }}</option>
        @endforeach
    </select>
    @error('jenis_keperluan')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Judul Keperluan --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengguna.form_judul_keperluan') }}</label>
    <input type="text" name="judul_keperluan" value="{{ old('judul_keperluan', $profile?->judul_keperluan) }}"
        placeholder="{{ __('cms.pengguna.form_judul_keperluan_placeholder') }}"
        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('judul_keperluan')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
