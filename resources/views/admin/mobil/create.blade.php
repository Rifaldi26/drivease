@extends('layouts.admin')
@section('title', 'Tambah Mobil')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.mobil.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-[#7a8499]
              hover:text-[#18213a] transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        Kembali ke Armada
    </a>
</div>

<x-page-header title="Tambah Mobil Baru" />

<form method="POST"
      action="{{ route('admin.mobil.store') }}"
      enctype="multipart/form-data"
      x-data="formMobil()">
    @csrf

    <div class="grid gap-4 lg:grid-cols-3">

        {{-- ── Kolom Kiri: Form Utama ─────────────────── --}}
        <div class="space-y-4 lg:col-span-2">

            {{-- Identitas Kendaraan --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-gray-900">
                    Identitas Kendaraan
                </h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input
                        name="nama"
                        label="Nama / Model"
                        placeholder="Toyota Avanza"
                        :value="old('nama')"
                        required />
                    <x-input
                        name="merek"
                        label="Merek"
                        placeholder="Toyota"
                        :value="old('merek')"
                        required />
                    <x-input
                        name="tahun"
                        label="Tahun"
                        type="number"
                        placeholder="2023"
                        :value="old('tahun')"
                        required />
                    <x-input
                        name="plat_nomor"
                        label="Plat Nomor"
                        placeholder="B 1234 ABC"
                        :value="old('plat_nomor')"
                        required />
                </div>
            </div>

            {{-- Harga Sewa --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-1 text-sm font-semibold text-gray-900">Harga Sewa</h3>
                <p class="mb-4 text-xs text-[#7a8499]">
                    Harga 12 jam bersifat opsional. Kosongkan jika tidak tersedia opsi sewa 12 jam.
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input
                        name="harga_per_hari"
                        label="Harga per Hari (Rp)"
                        type="number"
                        placeholder="350000"
                        :value="old('harga_per_hari')"
                        prefix="Rp"
                        required />
                    <div class="space-y-1">
                        <x-input
                            name="harga_12jam"
                            label="Harga 12 Jam (Rp)"
                            type="number"
                            placeholder="Kosongkan jika tidak ada"
                            :value="old('harga_12jam')"
                            prefix="Rp"
                            helper="Isi jika tersedia opsi sewa setengah hari" />
                    </div>
                </div>

                {{-- Preview kalkulasi --}}
                <div x-show="hargaHarian > 0 || harga12Jam > 0"
                     class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-gray-50 p-4 sm:grid-cols-4"
                     x-cloak>
                    <div>
                        <p class="text-xs text-[#7a8499]">Per Hari</p>
                        <p class="mt-0.5 text-sm font-semibold text-gray-900"
                           x-text="hargaHarian > 0 ? 'Rp ' + formatRp(hargaHarian) : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-[#7a8499]">Per 12 Jam</p>
                        <p class="mt-0.5 text-sm font-semibold text-gray-900"
                           x-text="harga12Jam > 0 ? 'Rp ' + formatRp(harga12Jam) : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-[#7a8499]">Contoh 3 Hari</p>
                        <p class="mt-0.5 text-sm font-semibold text-[#3b6fd4]"
                           x-text="hargaHarian > 0 ? 'Rp ' + formatRp(hargaHarian * 3) : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-[#7a8499]">Lebih Hemat</p>
                        <p class="mt-0.5 text-sm font-semibold text-green-600"
                           x-text="labelHematPerHari"></p>
                    </div>
                </div>
            </div>

            {{-- Opsi Supir --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-1 text-sm font-semibold text-gray-900">Opsi Supir</h3>
                <p class="mb-4 text-xs text-[#7a8499]">
                    Kosongkan jika kendaraan tidak menyediakan supir.
                </p>
                <x-input
                    name="biaya_supir_per_hari"
                    label="Biaya Supir per Hari (Rp)"
                    type="number"
                    placeholder="Kosongkan jika tidak ada"
                    :value="old('biaya_supir_per_hari')"
                    prefix="Rp"
                    helper="Biaya supir ditambahkan ke harga sewa (harian maupun 12 jam)" />
            </div>

            {{-- Deskripsi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <x-textarea
                    name="deskripsi"
                    label="Deskripsi Kendaraan"
                    placeholder="Kondisi kendaraan, fitur unggulan, kapasitas penumpang, dll."
                    rows="4"
                    helper="Maksimal 2000 karakter" />
            </div>

        </div>

        {{-- ── Kolom Kanan: Foto + Aksi ───────────────── --}}
        <div class="space-y-4">

            {{-- Upload Foto --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-gray-900">Foto Kendaraan</h3>

                <div x-data="uploadFoto()">

                    {{-- Preview / Drop Area --}}
                    <div @click="$refs.inputFoto.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="prosesFile($event.dataTransfer.files[0])"
                         :class="isDragging
                             ? 'border-[#3b6fd4] bg-[#eef2fb]'
                             : 'border-gray-200 bg-gray-50 hover:border-[#3b6fd4]/50 hover:bg-gray-100'"
                         class="relative cursor-pointer overflow-hidden rounded-xl border-2
                                border-dashed transition-colors">

                        {{-- Preview gambar --}}
                        <template x-if="preview">
                            <img :src="preview"
                                 class="h-48 w-full object-cover"
                                 alt="Preview foto">
                        </template>

                        {{-- Placeholder --}}
                        <template x-if="!preview">
                            <div class="flex h-48 flex-col items-center justify-center gap-2
                                        text-[#7a8499]">
                                <x-icon name="upload" class="w-8 h-8" />
                                <div class="text-center">
                                    <p class="text-sm font-medium">Klik atau seret foto ke sini</p>
                                    <p class="text-xs mt-0.5">JPG, PNG, WebP — maks. 2 MB</p>
                                </div>
                            </div>
                        </template>

                        {{-- Overlay ganti foto --}}
                        <template x-if="preview">
                            <div class="absolute inset-0 flex items-center justify-center
                                        bg-black/40 opacity-0 hover:opacity-100 transition-opacity">
                                <p class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold
                                          text-gray-900">
                                    Ganti Foto
                                </p>
                            </div>
                        </template>
                    </div>

                    <input type="file"
                           name="foto"
                           accept="image/jpeg,image/png,image/webp"
                           x-ref="inputFoto"
                           @change="prosesFile($event.target.files[0])"
                           class="hidden">

                    {{-- Error ukuran --}}
                    <p x-show="errorFoto"
                       x-text="errorFoto"
                       class="mt-2 text-xs text-red-600"
                       x-cloak></p>

                    {{-- Tombol hapus preview --}}
                    <template x-if="preview">
                        <button type="button"
                                @click.stop="hapusPreview()"
                                class="mt-2 flex w-full items-center justify-center gap-1.5
                                       rounded-lg border border-gray-200 py-2 text-xs
                                       font-medium text-[#7a8499] hover:bg-gray-50
                                       transition-colors">
                            <x-icon name="x" class="w-3.5 h-3.5" />
                            Hapus Foto
                        </button>
                    </template>
                </div>

                @error('foto')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-2">
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl
                               bg-[#3b6fd4] py-2.5 text-sm font-semibold text-white
                               hover:bg-[#2e5bb8] transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    Simpan Mobil
                </button>
                <a href="{{ route('admin.mobil.index') }}"
                   class="flex w-full items-center justify-center rounded-xl border
                          border-gray-200 py-2.5 text-sm font-medium text-[#7a8499]
                          hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>

            {{-- Info Ringkasan --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm text-xs
                        text-[#7a8499] space-y-2">
                <div class="flex items-start gap-2">
                    <x-icon name="info" class="w-4 h-4 flex-shrink-0 mt-0.5 text-[#3b6fd4]" />
                    <p>
                        Harga 12 jam dan opsi supir bersifat opsional.
                        Kosongkan jika tidak tersedia.
                    </p>
                </div>
                <div class="flex items-start gap-2">
                    <x-icon name="info" class="w-4 h-4 flex-shrink-0 mt-0.5 text-[#3b6fd4]" />
                    <p>
                        Status default kendaraan baru adalah
                        <strong class="text-gray-700">Tersedia</strong>.
                    </p>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function formMobil() {
    return {
        hargaHarian: {{ old('harga_per_hari', 0) }},
        harga12Jam:  {{ old('harga_12jam', 0) }},

        get labelHematPerHari() {
            if (!this.harga12Jam || !this.hargaHarian) return '—';
            const setengahHarian = this.hargaHarian / 2;
            const hemat          = setengahHarian - this.harga12Jam;
            if (hemat > 0) return 'Hemat Rp ' + this.formatRp(hemat);
            if (hemat < 0) return 'Lebih mahal Rp ' + this.formatRp(Math.abs(hemat));
            return 'Sama';
        },

        formatRp(n) {
            return Number(n).toLocaleString('id-ID');
        },

        init() {
            this.$watch('hargaHarian', v => { this.hargaHarian = parseFloat(v) || 0; });
            this.$watch('harga12Jam',  v => { this.harga12Jam  = parseFloat(v) || 0; });
        },
    }
}

function uploadFoto() {
    return {
        preview:    null,
        isDragging: false,
        errorFoto:  null,

        prosesFile(file) {
            this.errorFoto = null;
            if (!file) return;

            if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
                this.errorFoto = 'Format file tidak didukung. Gunakan JPG, PNG, atau WebP.';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                this.errorFoto = 'Ukuran file melebihi 2 MB.';
                return;
            }

            const reader  = new FileReader();
            reader.onload = e => { this.preview = e.target.result; };
            reader.readAsDataURL(file);
        },

        hapusPreview() {
            this.preview = null;
            this.$refs.inputFoto.value = '';
        },
    }
}
</script>
@endpush