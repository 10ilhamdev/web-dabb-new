<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Book;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    use \App\Traits\SwapsOrder;
    /**
     * Display a listing of books for a feature.
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $books = $feature->books()->orderBy('order')->get();

        return view('cms.features.virtual_books.index', compact('feature', 'books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $maxOrder = $feature->books()->max('order') ?? 0;

        return view('cms.features.virtual_books.create', compact('feature', 'maxOrder'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'total_pages' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|string|max:100',
            'isbn' => 'nullable|string|max:100',
            'synopsis' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'cover_position' => 'nullable|json',
            'cover_scale' => 'nullable|numeric|min:0.1|max:3',
            'cover_texts' => 'nullable|json',
            'title_position' => 'nullable|json',
            'back_title' => 'nullable|string|max:255',
            'back_cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'back_cover_position' => 'nullable|json',
            'back_cover_scale' => 'nullable|numeric|min:0.1|max:3',
            'back_title_position' => 'nullable|json',
            'back_cover_texts' => 'nullable|json',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'generated_thumbnail' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'pdf_path' => 'nullable|file|mimes:pdf|max:51200', // max 50MB
        ]);

        $validated['feature_id'] = $feature->id;

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('features/virtual-books/covers', 'public');
        }

        // Handle cover position
        if ($request->has('cover_position')) {
            $validated['cover_position'] = json_decode($request->cover_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle cover scale
        if ($request->has('cover_scale')) {
            $validated['cover_scale'] = $request->cover_scale;
        }

        // Handle cover texts (decode JSON string to array for Eloquent's array cast)
        if ($request->has('cover_texts')) {
            $validated['cover_texts'] = json_decode($request->cover_texts, true) ?? [];
        }

        // Handle title position
        if ($request->has('title_position')) {
            $validated['title_position'] = json_decode($request->title_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover image
        if ($request->hasFile('back_cover_image')) {
            $validated['back_cover_image'] = $request->file('back_cover_image')->store('features/virtual-books/covers', 'public');
        }

        // Handle back cover position
        if ($request->has('back_cover_position')) {
            $validated['back_cover_position'] = json_decode($request->back_cover_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover scale
        if ($request->has('back_cover_scale')) {
            $validated['back_cover_scale'] = $request->back_cover_scale;
        }

        // Handle back title position
        if ($request->has('back_title_position')) {
            $validated['back_title_position'] = json_decode($request->back_title_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover texts (decode JSON string to array for Eloquent's array cast)
        if ($request->has('back_cover_texts')) {
            $validated['back_cover_texts'] = json_decode($request->back_cover_texts, true) ?? [];
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('features/virtual-books/thumbnails', 'public');
        } elseif ($request->has('generated_thumbnail') && !empty($request->generated_thumbnail)) {
            // Handle generated thumbnail from preview (base64 data URL)
            $generatedThumbnail = $request->generated_thumbnail;
            if (preg_match('/^data:image\/(\w+);base64,/', $generatedThumbnail, $matches)) {
                $imageData = base64_decode(substr($generatedThumbnail, strpos($generatedThumbnail, ',') + 1));
                $extension = $matches[1];
                $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $extension;
                $path = 'features/virtual-books/thumbnails/' . $filename;
                Storage::disk('public')->put($path, $imageData);
                $validated['thumbnail'] = $path;
            }
        }

        // Handle PDF upload
        if ($request->hasFile('pdf_path')) {
            $path = $request->file('pdf_path')->store('features/virtual-books/pdfs', 'public');
            $validated['pdf_path'] = $path;
            
            // Auto-count pages from PDF and overwrite
            $validated['total_pages'] = $this->countPdfPages($path);
        }

        $book = $this->insertAndShiftOrder(Book::class, (int) $validated['order'], ['feature_id' => $feature->id], $validated);

        // Translate and save title_en for public display
        if (!empty($validated['title'])) {
            $book->update(['title_en' => $translationService->translate($validated['title'])]);
        }

        return redirect()->route('cms.features.virtual_books.index', $feature)
            ->with('success', __('cms.virtual_books.flash.created'));
    }

    /**
     * Show the form for editing a book.
     */
    public function edit(Feature $feature, Book $book)
    {
        $feature->load('parent');

        return view('cms.features.virtual_books.edit', compact('feature', 'book'));
    }

    /**
     * Update the book.
     */
    public function update(Request $request, Feature $feature, Book $book, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'total_pages' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|string|max:100',
            'isbn' => 'nullable|string|max:100',
            'synopsis' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'cover_position' => 'nullable|json',
            'cover_scale' => 'nullable|numeric|min:0.1|max:3',
            'cover_texts' => 'nullable|json',
            'title_position' => 'nullable|json',
            'back_title' => 'nullable|string|max:255',
            'back_cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'back_cover_position' => 'nullable|json',
            'back_cover_scale' => 'nullable|numeric|min:0.1|max:3',
            'back_title_position' => 'nullable|json',
            'back_cover_texts' => 'nullable|json',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'generated_thumbnail' => 'nullable|string',
            'remove_cover_image' => 'boolean',
            'remove_thumbnail' => 'boolean',
            'remove_back_cover_image' => 'boolean',
            'remove_pdf' => 'boolean',
            'order' => 'required|integer|min:0',
            'pdf_path' => 'nullable|file|mimes:pdf|max:51200', // max 50MB
        ]);

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('features/virtual-books/covers', 'public');
        } elseif ($request->boolean('remove_cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = null;
        }

        // Handle cover position
        if ($request->has('cover_position')) {
            $validated['cover_position'] = json_decode($request->cover_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle cover scale
        if ($request->has('cover_scale')) {
            $validated['cover_scale'] = $request->cover_scale;
        }

        // Handle cover texts (decode JSON string to array for Eloquent's array cast)
        if ($request->has('cover_texts')) {
            $validated['cover_texts'] = json_decode($request->cover_texts, true) ?? [];
        }

        // Handle title position
        if ($request->has('title_position')) {
            $validated['title_position'] = json_decode($request->title_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover image
        if ($request->hasFile('back_cover_image')) {
            if ($book->back_cover_image) {
                Storage::disk('public')->delete($book->back_cover_image);
            }
            $validated['back_cover_image'] = $request->file('back_cover_image')->store('features/virtual-books/covers', 'public');
        } elseif ($request->boolean('remove_back_cover_image')) {
            if ($book->back_cover_image) {
                Storage::disk('public')->delete($book->back_cover_image);
            }
            $validated['back_cover_image'] = null;
        }

        // Handle back cover position
        if ($request->has('back_cover_position')) {
            $validated['back_cover_position'] = json_decode($request->back_cover_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover scale
        if ($request->has('back_cover_scale')) {
            $validated['back_cover_scale'] = $request->back_cover_scale;
        }

        // Handle back title position
        if ($request->has('back_title_position')) {
            $validated['back_title_position'] = json_decode($request->back_title_position, true) ?? ['x' => 0, 'y' => 0];
        }

        // Handle back cover texts (decode JSON string to array for Eloquent's array cast)
        if ($request->has('back_cover_texts')) {
            $validated['back_cover_texts'] = json_decode($request->back_cover_texts, true) ?? [];
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($book->thumbnail) {
                Storage::disk('public')->delete($book->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('features/virtual-books/thumbnails', 'public');
        } elseif ($request->has('generated_thumbnail') && !empty($request->generated_thumbnail)) {
            // Handle generated thumbnail from preview (base64 data URL)
            $generatedThumbnail = $request->generated_thumbnail;
            if (preg_match('/^data:image\/(\w+);base64,/', $generatedThumbnail, $matches)) {
                // Delete old thumbnail if exists
                if ($book->thumbnail) {
                    Storage::disk('public')->delete($book->thumbnail);
                }
                $imageData = base64_decode(substr($generatedThumbnail, strpos($generatedThumbnail, ',') + 1));
                $extension = $matches[1];
                $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $extension;
                $path = 'features/virtual-books/thumbnails/' . $filename;
                Storage::disk('public')->put($path, $imageData);
                $validated['thumbnail'] = $path;
            }
        } elseif ($request->boolean('remove_thumbnail')) {
            if ($book->thumbnail) {
                Storage::disk('public')->delete($book->thumbnail);
            }
            $validated['thumbnail'] = null;
        }

        // Handle PDF upload
        if ($request->hasFile('pdf_path')) {
            if ($book->pdf_path) {
                Storage::disk('public')->delete($book->pdf_path);
            }
            $path = $request->file('pdf_path')->store('features/virtual-books/pdfs', 'public');
            $validated['pdf_path'] = $path;
            
            // Auto-count pages
            $validated['total_pages'] = $this->countPdfPages($path);
        } elseif ($request->boolean('remove_pdf')) {
            if ($book->pdf_path) {
                Storage::disk('public')->delete($book->pdf_path);
            }
            $validated['pdf_path'] = null;
        }

        $this->swapOrder($book, (int) $validated['order'], (int) $book->order, ['feature_id' => $book->feature_id]);
        $book->update($validated);

        // Translate and save title_en for public display
        if (!empty($validated['title'])) {
            $book->update(['title_en' => $translationService->translate($validated['title'])]);
        }

        return redirect()->route('cms.features.virtual_books.index', $feature)
            ->with('success', __('cms.virtual_books.flash.updated'));
    }

    /**
     * Remove the book.
     */
    public function destroy(Feature $feature, Book $book)
    {
        // Delete cover image
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Delete back cover image
        if ($book->back_cover_image) {
            Storage::disk('public')->delete($book->back_cover_image);
        }

        // Delete thumbnail
        if ($book->thumbnail) {
            Storage::disk('public')->delete($book->thumbnail);
        }

        // Delete PDF
        if ($book->pdf_path) {
            Storage::disk('public')->delete($book->pdf_path);
        }

        // Delete associated pages
        foreach ($book->pages as $page) {
            $images = $page->page_images ?? [];
            foreach ($images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        $book->pages()->delete();

        $this->deleteAndShiftOrder($book, ['feature_id' => $book->feature_id]);

        return redirect()->route('cms.features.virtual_books.index', $feature)
            ->with('success', __('cms.virtual_books.flash.deleted'));
    }

    /**
     * Display pages for a book.
     */
    public function pages(Feature $feature, Book $book)
    {
        $feature->load('parent');
        $book->load('pages');

        return view('cms.features.virtual_books.pages.index', compact('feature', 'book'));
    }
    /**
     * Helper to count PDF pages.
     */
    private function countPdfPages($path)
    {
        try {
            $filePath = Storage::disk('public')->path($path);
            if (!file_exists($filePath)) return 0;

            $content = file_get_contents($filePath);
            
            // More robust regex for PDF page counts (searching for /Type /Pages /Count X)
            // or just /Count X in the trailer/catalog
            if (preg_match('/\/Type\s*\/Pages\s*\/Count\s+(\d+)/', $content, $m)) {
                return (int)$m[1];
            }
            
            // Fallback to searching for all /Page tags (less accurate but better than nothing)
            if (preg_match_all('/\/Type\s*\/Page\b/', $content, $m)) {
                return count($m[0]);
            }

            // Simple fallback
            if (preg_match('/\/Count\s+(\d+)/', $content, $m)) {
                return (int)$m[1];
            }
            
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
