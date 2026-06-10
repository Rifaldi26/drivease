<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Tampilkan daftar semua halaman statis yang dapat diedit.
     */
    public function index()
    {
        $pages = Page::orderBy('slug')->get();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Tampilkan form editor untuk satu halaman.
     */
    public function edit(string $slug)
    {
        $page    = Page::where('slug', $slug)->firstOrFail();
        $content = rescue(
            fn () => json_decode($page->content, associative: true, flags: JSON_THROW_ON_ERROR),
            ['sections' => []],
        );

        return view('admin.pages.edit', compact('page', 'content'));
    }

    /**
     * Simpan perubahan halaman ke database.
     */
    public function update(UpdatePageRequest $request, string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        $page->update([
            'title'   => $request->validated('title'),
            'content' => json_encode(
                ['sections' => $request->validated('sections', [])],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', "\"{$page->title}\" berhasil diperbarui.");
    }
}