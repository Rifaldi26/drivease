@extends('layouts.admin')
@section('title', 'Edit ' . $mobil->nama)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.mobil.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-[#7a8499]
              hover:text-[#18213a] transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        Kembali ke Armada
    </a>
</div>

<x-page-header title="Edit {{ $mobil->nama }}" />

<form method="POST"
      action="{{ route('admin.mobil.update', $mobil) }}"
      enctype="multipart/form-data"
      x-data="formMobil(
          {{ old('harga_per_hari', $mobil->harga_per_hari) }},
          {{ old('harga_12jam',    $mobil->harga_12jam ?? 0) }}
      )">
    @csrf
    @method('PUT')

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
                        :value="old('nama', $mobil->nama)"
                        required />
                    <x-input
                        name="merek"
                        label="Merek"
                        :value="old('merek', $mobil->merek)"
                        required />
                    <x-input
                        name="tahun"
                        label="Tahun"
                        type="number"
                        :value="old('tahun', $mobil->tahun)"
                        required />
                    <x-input
                        name="plat_nomor"
                        label="Plat Nomor"
                        :value="old('plat_nomor', $mobil->plat_nomor)"
                        required />
                </div>
            </div>

            {{-- Harga Sewa --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-1 text-sm font-semibold text-gray-900">Harga Sewa</h3>
                <p class="mb-4 text-xs text-[#7a8499]">
                    Kosongkan harga 12 jam untuk menonaktifkan opsi sewa setengah hari.
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input
                        name="harga_per_hari"
                        label="Harga per Hari (Rp)"
                        type="number"
                        :value="old('harga_per_hari', $mobil->harga_per_hari)"
                        prefix="Rp"
                        x-model="hargaHarian"
                        required />
                    <x-input
                        name="harga_12jam"
                        label="Harga 12 Jam (Rp)"
                        type="number"
                        :value="old('harga_12jam', $mobil->harga_12jam)"
                        placeholder="Kosongkan jika tidak ada"
                        prefix="Rp"
                        x-model="harga12Jam"
                        helper="Isi jika tersedia opsi sewa setengah hari" />
                </div>

                {{-- Preview kalkulasi --}}
                <div x-show="hargaHarian > 0 || harga12Jam > 0"
                     class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-gray-50 p-4 sm:grid-cols-4">
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
                        <p class="text-xs text-[#7a8499]">Hemat 12 Jam</p>
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
                    :value="old('biaya_supir_per_hari', $mobil->biaya_supir_per_hari)"
                    placeholder="Kosongkan jika tidak ada"
                    prefix="Rp"
                    helper="Biaya supir berlaku untuk sewa harian maupun 12 jam" />
            </div>

            {{-- Deskripsi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <x-textarea
                    name="deskripsi"
                    label="Deskripsi Kendaraan"
                    rows="4"
                    helper="Maksimal 2000 karakter">{{ old('deskripsi', $mobil->deskripsi) }}</x-textarea>
            </div>

        </div>

        {{-- ── Kolom Kanan: Foto + Status + Aksi ─────── --}}
        <div class="space-y-4">

            {{-- Upload / Ganti Foto --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-gray-900">Foto Kendaraan</h3>

                <div x-data="uploadFotoEdit('{{ $mobil->foto ? Storage::url($mobil->foto) : '' }}')">

                    <div @click="$refs.inputFoto.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="prosesFile($event.dataTransfer.files[0])"
                         :class="isDragging
                             ? 'border-[#3b6fd4] bg-[#eef2fb]'
                             : 'border-gray-200 hover:border-[#3b6fd4]/50'"
                         class="relative cursor-pointer overflow-hidden rounded-xl
                                border-2 border-dashed transition-colors">

                        {{-- Preview / Foto Existing --}}
                        <template x-if="preview">
                            <img :src="preview"
                                 class="h-48 w-full object-cover"
                                 alt="Preview foto">
                        </template>

                        <template x-if="!preview">
                            <div class="flex h-48 flex-col items-center justify-center
                                        gap-2 text-[#7a8499] bg-gray-50">
                                <x-icon name="upload" class="w-8 h-8" />
                                <div class="text-center">
                                    <p class="text-sm font-medium">Klik untuk ganti foto</p>
                                    <p class="text-xs mt-0.5">JPG, PNG, WebP — maks. 2 MB</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="preview">
                            <div class="absolute inset-0 flex items-center justify-center
                                        bg-black/40 opacity-0 hover:opacity-100 transition-opacity">
                                <p class="rounded-lg bg-white px-3 py-1.5 text-xs
                                          font-semibold text-gray-900">
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

                    <p x-show="errorFoto"
                       x-text="errorFoto"
                       class="mt-2 text-xs text-red-600"
                       x-cloak></p>

                    {{-- Badge foto sudah ada --}}
                    @if($mobil->foto)
                        <p class="mt-2 flex items-center gap-1 text-xs text-green-600">
                            <x-icon name="check-circle" class="w-3.5 h-3.5" />
                            Foto sudah ada. Unggah baru untuk mengganti.
                        </p>
                    @endif
                </div>

                @error('foto')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Saat Ini --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Status Kendaraan</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <x-status-badge :status="$mobil->status">
                            {{ ucfirst($mobil->status) }}
                        </x-status-badge>
                        <p class="mt-1.5 text-xs text-[#7a8499]">
                            @if($mobil->status === 'disewa')
                                Status tidak dapat diubah saat kendaraan disewa.
                            @else
                                Ubah via tombol toggle di halaman daftar armada.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-2">
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl
                               bg-[#3b6fd4] py-2.5 text-sm font-semibold text-white
                               hover:bg-[#2e5bb8] transition-colors">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.mobil.index') }}"
                   class="flex w-full items-center justify-center rounded-xl border
                          border-gray-200 py-2.5 text-sm font-medium text-[#7a8499]
                          hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>

            {{-- Zona Bahaya --}}
            @if($mobil->status !== 'disewa')
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-red-700">Zona Berbahaya</h3>
                <p class="mb-3 text-xs text-red-600">
                    Menghapus kendaraan bersifat permanen dan tidak dapat dibatalkan.
                </p>
                <button type="button"
                        @click="$dispatch('open-modal-hapus-{{ $mobil->id }}')"
                        class="flex w-full items-center justify-center gap-2 rounded-xl
                               border border-red-300 py-2.5 text-sm font-medium text-red-600
                               hover:bg-red-100 transition-colors">
                    <x-icon name="trash" class="w-4 h-4" />
                    Hapus Kendaraan
                </button>
            </div>

            {{-- Modal Hapus --}}
            <x-modal id="hapus-{{ $mobil->id }}" title="Hapus Kendaraan" size="sm">
                <p class="text-sm text-gray-600">
                    Yakin ingin menghapus
                    <strong class="text-gray-900">{{ $mobil->nama }}</strong>?
                    Semua data terkait akan ikut terhapus dan tidak dapat dipulihkan.
                </p>
                <x-slot:footer>
                    <button @click="$dispatch('close-modal-hapus-{{ $mobil->id }}')"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-sm
                                   font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <form method="POST"
                          action="{{ route('admin.mobil.destroy', $mobil) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium
                                       text-white hover:bg-red-700 transition-colors">
                            Hapus Permanen
                        </button>
                    </form>
                </x-slot:footer>
            </x-modal>
            @endif

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function formMobil(hariAwal = 0, jamAwal = 0) {
    return {
        hargaHarian: parseFloat(hariAwal) || 0,
        harga12Jam:  parseFloat(jamAwal)  || 0,

        get labelHematPerHari() {
            if (!this.harga12Jam || !this.hargaHarian) return '—';
            const setengahHarian = this.hargaHarian / 2;
            const hemat          = setengahHarian - this.harga12Jam;
            if (hemat > 0) return 'Hemat Rp ' + this.formatRp(hemat);
            if (hemat < 0) return '+Rp ' + this.formatRp(Math.abs(hemat));
            return 'Sama';
        },

        formatRp(n) {
            return Number(n).toLocaleString('id-ID');
        },
    }
}

function uploadFotoEdit(fotoAwal = '') {
    return {
        preview:    fotoAwal || null,
        isDragging: false,
        errorFoto:  null,

        prosesFile(file) {
            this.errorFoto = null;
            if (!file) return;

            if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
                this.errorFoto = 'Format tidak didukung. Gunakan JPG, PNG, atau WebP.';
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
    }
}
</script>
@endpush