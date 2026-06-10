@extends('layouts.admin')
@section('title', 'Manajemen Halaman')

@section('content')

<x-page-header
    title="Manajemen Halaman"
    description="Kelola konten halaman statis yang ditampilkan kepada pengguna."
/>

{{-- Flash success --}}
@if (session('success'))
    <x-alert type="success" dismissible class="mb-6">
        {{ session('success') }}
    </x-alert>
@endif

<div class="overflow-hidden rounded-xl border border-[#e5e9f2] bg-white shadow-sm">

    @if ($pages->isEmpty())

        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center gap-3 py-20 text-center">
            <span class="grid h-14 w-14 place-items-center rounded-full bg-[#f1f4fb] text-[#3b6fd4]">
                <x-icon name="document-text" class="w-7 h-7" />
            </span>
            <p class="text-sm font-medium text-[#18213a]">Belum ada halaman statis</p>
            <p class="max-w-xs text-xs text-[#7a8499]">
                Halaman statis akan muncul di sini setelah data seeder dijalankan.
            </p>
        </div>

    @else

        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#e5e9f2] bg-[#f8fafc]">
                <tr>
                    <th class="px-6 py-4 font-semibold text-[#18213a]">Halaman</th>
                    <th class="px-6 py-4 font-semibold text-[#18213a]">Slug</th>
                    <th class="px-6 py-4 font-semibold text-[#18213a]">Terakhir Diperbarui</th>
                    <th class="px-6 py-4 font-semibold text-[#18213a] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e9f2]">
                @foreach ($pages as $page)
                    @php
                        $previewRoute = match ($page->slug) {
                            'terms'   => route('terms'),
                            'privacy' => route('privacy'),
                            default   => null,
                        };
                    @endphp
                    <tr class="hover:bg-[#f8fafc] transition-colors">

                        {{-- Nama --}}
                        <td class="px-6 py-4">
                            <p class="font-medium text-[#18213a]">{{ $page->title }}</p>
                        </td>

                        {{-- Slug --}}
                        <td class="px-6 py-4">
                            <code class="rounded bg-[#f1f4fb] px-2 py-0.5 text-xs text-[#3b6fd4]">
                                {{ $page->slug }}
                            </code>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 text-[#7a8499]">
                            {{ $page->updated_at->translatedFormat('d M Y, H:i') }} WIB
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Preview --}}
                                @if ($previewRoute)
                                    <a href="{{ $previewRoute }}"
                                       target="_blank"
                                       title="Lihat halaman"
                                       class="grid h-8 w-8 place-items-center rounded-lg border border-[#e5e9f2]
                                              text-[#7a8499] hover:bg-[#f1f4fa] hover:text-[#18213a] transition-colors">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                @endif

                                {{-- Edit --}}
                                <a href="{{ route('admin.pages.edit', $page->slug) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-[#eef2fb] px-3 py-1.5
                                          text-xs font-semibold text-[#3b6fd4]
                                          hover:bg-[#3b6fd4] hover:text-white transition-colors">
                                    <x-icon name="pencil" class="w-3.5 h-3.5" />
                                    Edit
                                </a>

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

</div>

@endsection