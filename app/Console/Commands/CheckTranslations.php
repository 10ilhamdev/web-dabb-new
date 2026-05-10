<?php

namespace App\Console\Commands;

use App\Models\FeaturePage;
use App\Models\FeaturePageSection;
use Illuminate\Console\Command;

class CheckTranslations extends Command
{
    protected $signature = 'check:translations';
    protected $description = 'Check which records are missing English translations';

    public function handle(): void
    {
        $pages = FeaturePage::whereNotNull('description')->get();
        $this->line('=== FeaturePages ===');
        $this->line('Total with description: ' . $pages->count());
        $this->line('Has description_en: ' . $pages->whereNotNull('description_en')->count());
        $this->line('Missing description_en: ' . $pages->whereNull('description_en')->count());
        $this->line('');
        foreach ($pages->take(10) as $p) {
            $this->line("ID={$p->id} | title={$p->title} | has_en=" . (!empty($p->description_en) ? 'YES' : 'NO'));
        }

        $sections = FeaturePageSection::whereNotNull('description')->get();
        $this->line('');
        $this->line('=== FeaturePageSections ===');
        $this->line('Total with description: ' . $sections->count());
        $this->line('Has description_en: ' . $sections->whereNotNull('description_en')->count());
        $this->line('Missing description_en: ' . $sections->whereNull('description_en')->count());
    }
}
