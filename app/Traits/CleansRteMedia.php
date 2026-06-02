<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * @method static void updating(\Closure|string|array $callback)
 * @method static void deleting(\Closure|string|array $callback)
 */
trait CleansRteMedia
{
    /**
     * Boot the trait to hook into updating and deleting events.
     */
    protected static function bootCleansRteMedia()
    {
        static::updating(function ($model) {
            $model->cleanRteMediaOnUpdate();
        });

        static::deleting(function ($model) {
            $model->cleanRteMediaOnDelete();
        });
    }

    /**
     * Get the fields that contain Rich Text HTML.
     * Default implementation checks common RTE field names.
     * Models can override this method if they use different field names.
     *
     * @return array
     */
    protected function getRteFields()
    {
        return [
            'content', 'content_en',
            'description', 'description_en',
            'synopsis', 'value'
        ];
    }

    /**
     * Clean up removed RTE media when updating the model.
     */
    protected function cleanRteMediaOnUpdate()
    {
        $fields = $this->getRteFields();
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->attributes)) {
                if ($this->isDirty($field)) {
                    $oldContent = $this->getOriginal($field);
                    $newContent = $this->getAttribute($field);
                    
                    $oldMedia = $this->extractRteMedia($oldContent);
                    $newMedia = $this->extractRteMedia($newContent);
                    
                    $toDelete = array_diff($oldMedia, $newMedia);
                    foreach ($toDelete as $file) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        }
    }

    /**
     * Clean up all RTE media when deleting the model.
     */
    protected function cleanRteMediaOnDelete()
    {
        $fields = $this->getRteFields();
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->attributes)) {
                $content = $this->getAttribute($field);
                $media = $this->extractRteMedia($content);
                foreach ($media as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

    /**
     * Extract all cms_media paths from HTML content.
     *
     * @param string|null $html
     * @return array
     */
    protected function extractRteMedia($html)
    {
        if (empty($html) || !is_string($html)) {
            return [];
        }

        $files = [];
        // Cari path gambar yang berada di folder cms_media (contoh: cms_media/12345.jpg)
        if (preg_match_all('/(cms_media\/[^"\'>\s]+)/i', $html, $matches)) {
            foreach ($matches[1] as $path) {
                $files[] = trim($path);
            }
        }
        
        return array_unique($files);
    }
}
