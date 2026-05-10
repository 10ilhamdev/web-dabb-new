<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    protected GoogleTranslate $translator;

    public function __construct()
    {
        $this->translator = new GoogleTranslate;
        $this->translator->setSource('id');
        $this->translator->setTarget('en');
    }

    /**
     * Translate a string from Indonesian to English.
     * Safely handles HTML by replacing tags with numbered placeholders,
     * translating the resulting text as one coherent chunk, then restoring tags.
     *
     * This preserves sentence context (better translation quality) and
     * ensures HTML attributes/tags (style, class, href, src, etc.) are never
     * sent to Google Translate.
     */
    public function translate(string $text): string
    {
        if (trim($text) === '') {
            return $text;
        }

        // If it doesn't look like HTML, translate normally
        if (strpos($text, '<') === false) {
            return $this->performTranslation($text);
        }

        return $this->translateHtml($text);
    }

    /**
     * Translate HTML content using placeholder technique:
     * 1. Replace all HTML tags with numbered placeholders (⟨0⟩, ⟨1⟩, …)
     * 2. Translate the resulting plain text as one coherent block
     * 3. Restore original HTML tags from the placeholders
     *
     * This gives Google Translate full sentence context while keeping
     * all HTML structure, attributes, and styling intact.
     */
    /**
     * Translate HTML content safely by splitting into logical blocks (li, p, etc.)
     * and translating each block individually. This prevents Google Translate
     * from merging or skipping content (like list points).
     */
    protected function translateHtml(string $html): string
    {
        // 1. Identify common block tags that should be treated as separate units
        $blockTags = ['li', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'tr', 'td', 'th', 'dt', 'dd', 'figcaption'];
        $tagPattern = implode('|', $blockTags);
        
        // Regex to capture <tag...>content</tag>
        $pattern = "/<({$tagPattern})(\b[^>]*)>(.*?)<\/\\1>/ius";
        
        $offset = 0;
        $result = '';
        
        // Iterate through all top-level blocks
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $fullMatch = $matches[0][0];
            $tagName = $matches[1][0];
            $attributes = $matches[2][0];
            $innerContent = $matches[3][0];
            $matchOffset = $matches[0][1];
            
            // Translate text before the block
            $prefix = substr($html, $offset, $matchOffset - $offset);
            if (trim(strip_tags($prefix)) !== '') {
                $result .= $this->translateFragment($prefix);
            } else {
                $result .= $prefix;
            }
            
            // Translate the block content
            if (trim(strip_tags($innerContent)) !== '') {
                $translatedInner = $this->translateFragment($innerContent);
                $result .= "<{$tagName}{$attributes}>{$translatedInner}</{$tagName}>";
            } else {
                $result .= $fullMatch;
            }
            
            $offset = $matchOffset + strlen($fullMatch);
        }
        
        // Translate remaining text
        $suffix = substr($html, $offset);
        if (trim(strip_tags($suffix)) !== '') {
            $result .= $this->translateFragment($suffix);
        } else {
            $result .= $suffix;
        }
        
        return $result;
    }

    /**
     * Translates a small fragment of text, protecting any remaining inline tags (b, i, a, span)
     * with placeholders.
     */
    protected function translateFragment(string $text): string
    {
        if (trim(strip_tags($text)) === '') {
            return $text;
        }

        $tags = [];
        $counter = 0;

        // Replace remaining tags with placeholders
        $stripped = preg_replace_callback('/<[^>]+>/u', function ($m) use (&$tags, &$counter) {
            $key = "[[t{$counter}]]";
            $tags[$key] = $m[0];
            $counter++;
            return $key;
        }, $text);

        // Perform translation
        $translated = $this->performTranslation($stripped);

        // Restore tags with resilience for mangled placeholders (spaces, case)
        $keys = array_keys($tags);
        usort($keys, function($a, $b) { return strlen($b) - strlen($a); });

        foreach ($keys as $key) {
            $tag = $tags[$key];
            
            // Try exact match
            if (strpos($translated, $key) !== false) {
                $translated = str_replace($key, $tag, $translated);
                continue;
            }

            // Try common mangled versions (e.g. [[ t 0 ]], [[t 0]])
            $num = str_replace(['[[t', ']]'], '', $key);
            $patterns = [
                '/\[\[\s*t\s*' . $num . '\s*\]\]/ui', // [[ t 0 ]]
                '/\[\s*\[\s*t\s*' . $num . '\s*\]\s*\]/ui', // [ [ t 0 ] ]
            ];
            
            $restored = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $translated)) {
                    $translated = preg_replace($pattern, $tag, $translated);
                    $restored = true;
                    break;
                }
            }
        }

        return $translated;
    }

    /**
     * Internal method to perform the actual translation via Google Translate.
     */
    protected function performTranslation(string $text): string
    {
        try {
            $translated = $this->translator->translate($text) ?? $text;
            return htmlspecialchars_decode($translated, ENT_QUOTES | ENT_HTML5);
        } catch (\Exception $e) {
            Log::warning('Translation failed: ' . $e->getMessage(), ['text' => mb_substr($text, 0, 100)]);
            return $text;
        }
    }

    /**
     * Recursively translate all string values in an array.
     * Skips keys that should not be translated (URLs, IDs, emails, etc).
     */
    public function translateArray(array $data, array $skipKeys = []): array
    {
        $defaultSkipKeys = ['youtube_ids', 'phone', 'email', 'address', 'copyright', 'path'];
        $skipKeys = array_merge($defaultSkipKeys, $skipKeys);
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                $result[$key] = $value;

                continue;
            }

            if (is_array($value)) {
                $result[$key] = $this->translateArray($value, $skipKeys);
            } elseif (is_string($value) && trim($value) !== '') {
                $result[$key] = $this->translate($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
