<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends Controller
{
    /**
     * Show the Beranda content editor.
     */
    public function edit(int $featureId)
    {
        $feature = Feature::findOrFail($featureId);

        // Load data file specific to this feature
        $idContent = $this->loadLangFile('id', $featureId);
        $enContent = $this->loadLangFile('en', $featureId);

        return view('cms.home.edit', compact('feature', 'idContent', 'enContent'));
    }

    /**
     * Save the Beranda content.
     * Saves Indonesian text, then auto-translates and saves to English.
     */
    public function update(Request $request, TranslationService $translationService, int $featureId)
    {
        $feature = Feature::findOrFail($featureId);

        $data = $request->except(['_token', '_method', 'locale', 'info_image_1', 'info_image_2', 'stats_image', 'photo_file', 'hero_background_file', 'hero_background_remove']);

        // Handle hero background upload (image or video)
        $existingForHero = $this->loadLangFile('id', $featureId);
        $oldHeroPath = $existingForHero['hero']['background_path'] ?? null;

        if ($request->boolean('hero_background_remove')) {
            if ($oldHeroPath) {
                Storage::disk('public')->delete($oldHeroPath);
            }
            $data['hero']['background_path'] = '';
            $data['hero']['background_type'] = '';
        } elseif ($request->hasFile('hero_background_file')) {
            $file = $request->file('hero_background_file');
            if ($file && $file->isValid()) {
                $mime = (string) $file->getMimeType();
                $ext = strtolower($file->getClientOriginalExtension());

                $type = null;
                if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg','jpeg','png','webp','gif','avif'])) {
                    $type = 'image';
                } elseif (str_starts_with($mime, 'video/') || in_array($ext, ['mp4','webm','ogg','mov'])) {
                    $type = 'video';
                }

                if ($type) {
                    $path = $file->store('home/hero', 'public');
                    if ($oldHeroPath) {
                        Storage::disk('public')->delete($oldHeroPath);
                    }
                    $data['hero']['background_path'] = $path;
                    $data['hero']['background_type'] = $type;
                }
            }
        } else {
            // No new upload and no removal: preserve the hidden-field values that were submitted
            // (these already came through $request->input('hero.*') via $data).
            // Normalize missing keys so merge doesn't wipe them.
            if (!isset($data['hero']) || !is_array($data['hero'])) {
                $data['hero'] = [];
            }
            $data['hero']['background_path'] = $data['hero']['background_path'] ?? ($oldHeroPath ?? '');
            $data['hero']['background_type'] = $data['hero']['background_type'] ?? ($existingForHero['hero']['background_type'] ?? '');
        }

        // Handle info section image uploads
        foreach ([1, 2] as $num) {
            $fieldName = "info_image_{$num}";
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $path = $file->store('home/info', 'public');
                // Delete old image if exists
                $existing = $this->loadLangFile('id', $featureId);
                $oldPath = $existing['sections']["info_image_{$num}"] ?? null;
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data['sections']["info_image_{$num}"] = $path;
            }
        }

        // Handle stats image upload
        if ($request->hasFile('stats_image')) {
            $file = $request->file('stats_image');
            $path = $file->store('home/stats', 'public');
            $existing = $this->loadLangFile('id', $featureId);
            $oldPath = $existing['stats']['image'] ?? null;
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $data['stats']['image'] = $path;
        }

        // Handle related_links photo uploads and deletions
        $existing = $this->loadLangFile('id', $featureId);
        $existingRelatedLinks = $existing['feature_strip']['related_links'] ?? [];

        if (isset($data['feature_strip']['related_links']) && is_array($data['feature_strip']['related_links'])) {
            foreach ($data['feature_strip']['related_links'] as $index => &$link) {
                // Use dot notation for more reliable file upload detection
                $photoFieldName = "feature_strip.related_links.{$index}.photo_file";
                if ($request->hasFile($photoFieldName)) {
                    $file = $request->file($photoFieldName);
                    if ($file && $file->isValid()) {
                        $path = $file->store('home/related', 'public');
                        // Delete old image if exists
                        $oldPath = $existingRelatedLinks[$index]['photo'] ?? null;
                        if ($oldPath) {
                            Storage::disk('public')->delete($oldPath);
                        }
                        $link['photo'] = $path;
                    }
                } else {
                    // Keep existing photo path from hidden field
                    $link['photo'] = $request->input("feature_strip.related_links.{$index}.photo", '');
                }
                // Remove the photo_file key (contains UploadedFile object that can't be serialized)
                unset($link['photo_file']);
                // Remove empty link entries
                if (empty($link['title']) && empty($link['link']) && empty($link['photo'])) {
                    unset($data['feature_strip']['related_links'][$index]);
                }
            }
            unset($link);
            // Reindex array
            $data['feature_strip']['related_links'] = array_values($data['feature_strip']['related_links']);
        } else {
            // No related_links submitted = user deleted all links
            if (!isset($data['feature_strip'])) {
                $data['feature_strip'] = [];
            }
            $data['feature_strip']['related_links'] = [];
        }

        // Delete orphaned photos from storage (photos from links that were removed)
        $newLinks = $data['feature_strip']['related_links'];
        $newPhotos = array_filter(array_column($newLinks, 'photo'));
        foreach ($existingRelatedLinks as $oldLink) {
            $oldPhoto = $oldLink['photo'] ?? '';
            if ($oldPhoto && !in_array($oldPhoto, $newPhotos)) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        // related_links must be fully replaced (not deep-merged) so deleted items don't persist
        $replaceKeys = [['feature_strip', 'related_links']];

        // 1. Save Indonesian version
        $this->saveLangFile('id', $data, $featureId, $replaceKeys);

        // 2. Load full ID file, translate, and save EN version
        // Skip keys that store file paths / structural values (not user-visible text).
        $fullIdContent = $this->loadLangFile('id', $featureId);
        $translatedData = $translationService->translateArray($fullIdContent, [
            'background_path', 'background_type', 'image', 'photo', 'stats_image',
        ]);
        $this->saveLangFile('en', $translatedData, $featureId, $replaceKeys);

        return redirect()->route('cms.features.index', $featureId)
            ->with('success', 'Konten Beranda berhasil disimpan');
    }

    /**
     * Load language file as array.
     * For feature ID 1, always prioritize original home.php if it exists.
     */
    private function loadLangFile(string $locale, int $featureId): array
    {
        // For feature ID 1, always try original home.php first
        if ($featureId == 1) {
            $originalPath = resource_path("lang/{$locale}/home.php");
            if (File::exists($originalPath)) {
                $content = include $originalPath;
                if (!empty($content)) {
                    return $content;
                }
            }
        }

        // Otherwise use feature-specific file
        $path = resource_path("lang/{$locale}/home_{$featureId}.php");
        if (File::exists($path)) {
            return include $path;
        }

        return [];
    }

    /**
     * Save data back to language file.
     * For new beranda pages, save only submitted data. For original beranda, merge with existing.
     */
    private function saveLangFile(string $locale, array $data, int $featureId, array $replaceKeys = []): void
    {
        // For feature ID 1, always save to original home.php (with merge to preserve old data)
        if ($featureId == 1) {
            $path = resource_path("lang/{$locale}/home.php");
            $existing = File::exists($path) ? include $path : [];

            // Before merging, clear specific keys so mergeDeep won't carry over deleted items.
            // e.g. ['feature_strip', 'related_links'] => clear $existing['feature_strip']['related_links']
            foreach ($replaceKeys as $keyPath) {
                $ref = &$existing;
                $lastKey = array_pop($keyPath);
                foreach ($keyPath as $segment) {
                    if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                        continue 2; // path doesn't exist, skip
                    }
                    $ref = &$ref[$segment];
                }
                if (isset($ref[$lastKey])) {
                    $ref[$lastKey] = [];
                }
                unset($ref);
            }

            $updated = $this->mergeDeep($existing, $data);
        } else {
            // For new beranda pages (feature ID > 1), only save the data that was submitted
            // Filter out empty/null values to keep file clean
            $path = resource_path("lang/{$locale}/home_{$featureId}.php");
            $updated = $this->filterEmptyValues($data);
        }

        // Write back as PHP array
        $content = "<?php\n\nreturn ".$this->varExport($updated, true).";\n";
        File::put($path, $content);
    }

    /**
     * Filter out empty/null values from data.
     */
    private function filterEmptyValues(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->filterEmptyValues($value);
                if (empty($data[$key])) {
                    unset($data[$key]);
                }
            } elseif ($value === null || $value === '') {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Deep merge arrays.
     */
    private function mergeDeep(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->mergeDeep($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }

    /**
     * Export variable as formatted PHP code.
     */
    private function varExport($var, bool $indent = false, int $level = 0): string
    {
        if (is_array($var)) {
            $pad = str_repeat('    ', $level + 1);
            $closePad = str_repeat('    ', $level);
            $items = [];
            $isList = array_keys($var) === range(0, count($var) - 1);
            foreach ($var as $k => $v) {
                $key = $isList ? '' : var_export($k, true).' => ';
                $items[] = $pad.$key.$this->varExport($v, $indent, $level + 1);
            }

            return "[\n".implode(",\n", $items).",\n{$closePad}]";
        }

        return var_export($var, true);
    }
}
