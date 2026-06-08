@extends('layouts.app')
@section('title', $page->title)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="rounded-2xl bg-white p-8 shadow-sm border border-[#e5e9f2]">
        <h1 class="mb-6 text-center text-2xl font-bold text-[#18213a] uppercase">{{ $page->title }}</h1>
        
        {{-- Tanggal Otomatis --}}
        <p class="mt-2 text-center text-[#7a8499]">
            Terakhir diperbarui : {{ $page->updated_at->translatedFormat('j F Y') }}
        </p>
        
        <div class="prose prose-sm max-w-none text-[#18213a] mt-8">
            {{-- Menampilkan isi konten dari database (tag HTML tidak di-escape) --}}
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection