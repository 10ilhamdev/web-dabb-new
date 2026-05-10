<?php

namespace App\Console\Commands;

use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\FeaturePageSection;
use App\Models\VirtualSlideshowPage;
use App\Models\VirtualSlideshowSlide;
use App\Services\TranslationService;
use Illuminate\Console\Command;

class RetranslateContent extends Command
{
    protected $signature = 'translate:retranslate
                            {--model=all : Which model to retranslate (all|features|pages|sections|slideshow_pages|slideshow_slides)}
                            {--force : Re-translate even if _en field already exists}
                            {--dry-run : Show what would be translated without actually saving}
                            {--id= : Translate only this specific record ID}';

    protected $description = 'Re-translate all content from Indonesian to English, fixing missing or broken translations';

    public function handle(TranslationService $translationService): int
    {
        $model    = $this->option('model');
        $force    = $this->option('force');
        $dryRun   = $this->option('dry-run');
        $specificId = $this->option('id');

        if ($dryRun) {
            $this->warn('[DRY RUN] No data will be saved.');
        }

        $models = match ($model) {
            'features'         => ['features'],
            'pages'            => ['pages'],
            'sections'         => ['sections'],
            'profiles'         => ['profiles'],
            'profile_sections' => ['profile_sections'],
            'slideshow_pages'  => ['slideshow_pages'],
            'slideshow_slides' => ['slideshow_slides'],
            default            => ['features', 'pages', 'sections', 'profiles', 'profile_sections', 'slideshow_pages', 'slideshow_slides'],
        };

        $totalUpdated = 0;
        $totalSkipped = 0;
        $totalFailed  = 0;

        foreach ($models as $m) {
            [$updated, $skipped, $failed] = $this->processModel(
                $m, $translationService, $force, $dryRun, $specificId
            );
            $totalUpdated += $updated;
            $totalSkipped += $skipped;
            $totalFailed  += $failed;
        }

        $this->newLine();
        $this->info("Done! Updated: {$totalUpdated} | Skipped: {$totalSkipped} | Failed: {$totalFailed}");

        return 0;
    }

    private function processModel(string $model, TranslationService $ts, bool $force, bool $dryRun, ?string $specificId): array
    {
        $updated = 0;
        $skipped = 0;
        $failed  = 0;

        [$query, $fields] = match ($model) {
            'features' => [
                Feature::query(),
                [
                    'name'    => 'name_en',
                    'content' => 'content_en',
                ],
            ],
            'pages' => [
                FeaturePage::query(),
                [
                    'title'       => 'title_en',
                    'description' => 'description_en',
                    'subtitle'    => 'subtitle_en',
                ],
            ],
            'sections' => [
                FeaturePageSection::query(),
                [
                    'title'       => 'title_en',
                    'description' => 'description_en',
                ],
            ],
            'profiles' => [
                \App\Models\Profile::query(),
                [
                    'title'       => 'title_en',
                    'subtitle'    => 'subtitle_en',
                    'description' => 'description_en',
                ],
            ],
            'profile_sections' => [
                \App\Models\ProfileSection::query(),
                [
                    'title'       => 'title_en',
                    'description' => 'description_en',
                ],
            ],
            'slideshow_pages' => [
                VirtualSlideshowPage::query(),
                [
                    'title'       => 'title_en',
                    'description' => 'description_en',
                ],
            ],
            'slideshow_slides' => [
                VirtualSlideshowSlide::query(),
                [
                    'title'       => 'title_en',
                    'description' => 'description_en',
                ],
            ],
            default => [null, []],
        };

        if ($query === null) {
            return [0, 0, 0];
        }

        if ($specificId) {
            $query->where('id', $specificId);
        }

        $records = $query->get();
        $this->line("\n[{$model}] Processing {$records->count()} records...");
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $record) {
            $changes = [];
            $needsUpdate = false;

            foreach ($fields as $srcField => $dstField) {
                $src = (string) $record->{$srcField};
                $dst = (string) $record->{$dstField};

                // Skip if source is empty
                if (empty(trim(strip_tags($src)))) {
                    continue;
                }

                // Skip if destination already exists and not forcing
                if (!$force && !empty(trim(strip_tags($dst)))) {
                    continue;
                }

                // Attempt translation
                try {
                    $translated = $ts->translate($src);

                    // Only update if translation is different from source
                    if ($translated !== $src && !empty(trim(strip_tags($translated)))) {
                        $changes[$dstField] = $translated;
                        $needsUpdate = true;
                        
                        if ($this->option('verbose')) {
                            $this->line("\n  [TRANSLATED] ID={$record->id} {$srcField} -> {$dstField}");
                        }
                    } else {
                        // Translation returned same text or empty — skip
                        if ($this->option('verbose')) {
                            $this->line("\n  [SKIP] No change for ID={$record->id} field={$srcField}");
                        }
                    }
                } catch (\Throwable $e) {
                    $this->line("\n  [ERROR] ID={$record->id} field={$srcField}: " . $e->getMessage());
                    $failed++;
                }
            }

            if ($needsUpdate) {
                if (!$dryRun) {
                    try {
                        $record->fill($changes);
                        if ($record->save()) {
                            $updated++;
                        } else {
                            $this->line("\n  [ERROR] Failed to save ID={$record->id}");
                            $failed++;
                        }
                    } catch (\Exception $e) {
                        $this->line("\n  [ERROR] Database error ID={$record->id}: " . $e->getMessage());
                        $failed++;
                    }
                } else {
                    foreach ($changes as $k => $v) {
                        $this->line("\n  [DRY] ID={$record->id} {$k}=" . mb_substr(strip_tags($v), 0, 100) . "...");
                    }
                    $updated++;
                }
            } else {
                $skipped++;
            }

            $bar->advance();

            // Small delay to avoid hitting Google rate limits
            usleep(100_000); // 100ms
        }

        $bar->finish();
        $this->line("\n  [{$model}] Result -> Updated: {$updated} | Skipped: {$skipped} | Failed: {$failed}");

        return [$updated, $skipped, $failed];
    }
}
