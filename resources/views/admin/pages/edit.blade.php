@extends('layouts.admin')
@section('title', 'Edit ' . $page->title)

@section('content')

<div class="space-y-6 max-w-4xl">

    {{-- Header --}}
    <x-page-header :title="'Edit: ' . $page->title">
        <x-slot name="actions">
            @php
                $previewRoute = match ($page->slug) {
                    'terms'   => route('terms'),
                    'privacy' => route('privacy'),
                    default   => null,
                };
            @endphp
            @if ($previewRoute)
                <a href="{{ $previewRoute }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-[#e5e9f2]
                          px-3 py-2 text-sm text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                    <x-icon name="eye" class="w-4 h-4" />
                    Lihat Halaman
                </a>
            @endif
            <a href="{{ route('admin.pages.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-[#e5e9f2]
                      px-3 py-2 text-sm text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Kembali
            </a>
        </x-slot>
    </x-page-header>

    {{-- Validation errors --}}
    @if ($errors->any())
        <x-alert type="error" dismissible>
            <p class="font-medium">Terdapat kesalahan pada form:</p>
            <ul class="mt-1 list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form action="{{ route('admin.pages.update', $page->slug) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div class="rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm">
            <label class="mb-1.5 block text-sm font-medium text-[#18213a]">
                Judul Halaman
                <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="title"
                value="{{ old('title', $page->title) }}"
                required
                class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition-all
                       focus:border-[#3b6fd4] focus:ring-1 focus:ring-[#3b6fd4]
                       @error('title') border-red-400 bg-red-50 @else border-[#e5e9f2] @enderror"
            >
            @error('title')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sections --}}
        <div id="sections-wrapper" class="space-y-6">
            @forelse ($content['sections'] as $si => $section)
                @include('admin.pages._section', [
                    'si'      => $si,
                    'section' => $section,
                ])
            @empty
                {{-- Seeder belum dijalankan atau content kosong --}}
            @endforelse
        </div>

        {{-- Tambah Section --}}
        <button
            type="button"
            onclick="addSection()"
            class="w-full rounded-xl border border-dashed border-[#7a8499] py-3 text-sm
                   text-[#7a8499] hover:bg-[#f8f9fc] transition-colors"
        >
            + Tambah Section
        </button>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pages.index') }}"
               class="rounded-lg border border-[#e5e9f2] px-5 py-2.5 text-sm text-[#7a8499]
                      hover:bg-[#f1f4fa] transition-colors">
                Batal
            </a>
            <button
                type="submit"
                class="rounded-lg bg-[#3b6fd4] px-6 py-2.5 text-sm font-semibold text-white
                       hover:bg-[#2e5bb8] transition-colors"
            >
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@endsection

{{-- Template HTML untuk section dan item baru (disembunyikan) --}}
<template id="tpl-section">
    <div class="section-block rounded-xl border border-[#e5e9f2] bg-white p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <p class="section-label text-sm font-bold text-[#18213a]"></p>
            <button type="button" onclick="removeSection(this)"
                    class="text-xs text-red-500 hover:text-red-700">
                Hapus Section
            </button>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#18213a]">
                Judul Section <span class="text-red-500">*</span>
            </label>
            <input type="text" data-field="title" value=""
                   class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm
                          focus:border-[#3b6fd4] focus:ring-1 focus:ring-[#3b6fd4] outline-none transition-all">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#18213a]">
                Paragraf Pembuka
                <span class="text-[#7a8499] font-normal">(opsional)</span>
            </label>
            <textarea data-field="intro" rows="2"
                      class="w-full rounded-lg border border-[#e5e9f2] px-4 py-2.5 text-sm
                             focus:border-[#3b6fd4] focus:ring-1 focus:ring-[#3b6fd4] outline-none
                             transition-all resize-none"></textarea>
        </div>
        <div class="items-wrapper space-y-3"></div>
        <button type="button" onclick="addItem(this)"
                class="w-full rounded-lg border border-dashed border-[#3b6fd4] py-2 text-sm
                       text-[#3b6fd4] hover:bg-[#f0f4fc] transition-colors">
            + Tambah Poin
        </button>
    </div>
</template>

<template id="tpl-item">
    <div class="item-block rounded-lg bg-[#f8f9fc] p-4 space-y-2">
        <div class="flex items-center justify-between">
            <p class="item-label text-xs font-semibold text-[#7a8499] uppercase"></p>
            <button type="button" onclick="removeItem(this)"
                    class="text-xs text-red-400 hover:text-red-600">
                Hapus
            </button>
        </div>
        <div>
            <label class="mb-1 block text-xs text-[#18213a]">
                Label <span class="text-[#7a8499]">(bold, opsional)</span>
            </label>
            <input type="text" data-field="label" value=""
                   class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm
                          focus:border-[#3b6fd4] outline-none transition-all">
        </div>
        <div>
            <label class="mb-1 block text-xs text-[#18213a]">Isi <span class="text-red-500">*</span></label>
            <textarea data-field="text" rows="2"
                      class="w-full rounded-lg border border-[#e5e9f2] px-3 py-2 text-sm
                             focus:border-[#3b6fd4] outline-none transition-all resize-none"></textarea>
        </div>
    </div>
</template>

@push('scripts')
<script>
/**
 * Re-index semua name attribute setelah setiap perubahan struktur.
 *
 * Pendekatan: rebuild nama dari data-field attribute alih-alih
 * regex replace, sehingga tidak ada edge case saat index berubah.
 */
function reindex() {
    document.querySelectorAll('#sections-wrapper .section-block').forEach((section, si) => {
        section.dataset.si = si;
        section.querySelector('.section-label').textContent = `Section ${si + 1}`;

        // Field langsung di section (title, intro)
        section.querySelectorAll(':scope > div > [data-field]').forEach(el => {
            const field = el.dataset.field;
            el.name = `sections[${si}][${field}]`;
        });

        // Items di dalam section
        section.querySelectorAll('.item-block').forEach((item, ii) => {
            item.querySelector('.item-label').textContent = `Poin ${ii + 1}`;
            item.querySelectorAll('[data-field]').forEach(el => {
                const field = el.dataset.field;
                el.name = `sections[${si}][items][${ii}][${field}]`;
            });
        });
    });
}

function addSection() {
    const clone = document.getElementById('tpl-section').content.cloneNode(true);
    document.getElementById('sections-wrapper').appendChild(clone);
    reindex();
}

function removeSection(btn) {
    const sections = document.querySelectorAll('#sections-wrapper .section-block');
    if (sections.length <= 1) {
        alert('Minimal harus ada 1 section.');
        return;
    }
    btn.closest('.section-block').remove();
    reindex();
}

function addItem(btn) {
    const wrapper = btn.previousElementSibling; // .items-wrapper
    const clone   = document.getElementById('tpl-item').content.cloneNode(true);
    wrapper.appendChild(clone);
    reindex();
}

function removeItem(btn) {
    btn.closest('.item-block').remove();
    reindex();
}
</script>
@endpush