<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarHolidayService
{
    /**
     * Get an array of holidays formatted as [ 'YYYY-MM-DD' => 'Holiday Name' ]
     * Fetches from Google Calendar public Indonesian holidays ICS feed.
     */
    public static function getHolidays($year = null)
    {
        if (!$year) {
            $year = now()->year;
        }

        return Cache::remember('google_indonesian_holidays_' . $year, 86400, function () use ($year) {
            try {
                $url = 'https://calendar.google.com/calendar/ical/id.indonesian%23holiday%40group.v.calendar.google.com/public/basic.ics';
                $response = Http::timeout(15)->get($url);

                if (!$response->successful()) {
                    Log::warning("Google Calendar ICS fetch returned status: " . $response->status());
                    return self::getFallbackHolidays($year);
                }

                $lines = explode("\n", $response->body());
                $holidays = [];
                $currentEvent = null;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === 'BEGIN:VEVENT') {
                        $currentEvent = [];
                    } elseif ($line === 'END:VEVENT') {
                        if (!empty($currentEvent['dtstart']) && !empty($currentEvent['summary'])) {
                            $desc = $currentEvent['description'] ?? '';
                            $summary = $currentEvent['summary'];
                            
                            // Validate if it's an official national holiday or cuti bersama
                            $isHoliday = str_contains(strtolower($desc), 'libur nasional') || 
                                         str_contains(strtolower($summary), 'cuti bersama') ||
                                         str_contains(strtolower($summary), 'idul fitri') ||
                                         str_contains(strtolower($summary), 'idul adha') ||
                                         str_contains(strtolower($summary), 'tahun baru') ||
                                         str_contains(strtolower($summary), 'nyepi') ||
                                         str_contains(strtolower($summary), 'waisak') ||
                                         str_contains(strtolower($summary), 'pancasila') ||
                                         str_contains(strtolower($summary), 'kemerdekaan') ||
                                         str_contains(strtolower($summary), 'natal') ||
                                         str_contains(strtolower($summary), 'wafat') ||
                                         str_contains(strtolower($summary), 'paskah') ||
                                         str_contains(strtolower($summary), 'kenaikan') ||
                                         str_contains(strtolower($summary), 'isra mikraj') ||
                                         str_contains(strtolower($summary), 'maulid') ||
                                         str_contains(strtolower($summary), 'imlek');

                            if ($isHoliday) {
                                $dateStr = $currentEvent['dtstart'];
                                if (strlen($dateStr) === 8) {
                                    $y = substr($dateStr, 0, 4);
                                    $m = substr($dateStr, 4, 2);
                                    $d = substr($dateStr, 6, 2);
                                    $formattedDate = "$y-$m-$d";
                                    
                                    if ((int)$y === (int)$year) {
                                        $holidays[$formattedDate] = $summary;
                                    }
                                }
                            }
                        }
                        $currentEvent = null;
                    } elseif ($currentEvent !== null) {
                        if (str_starts_with($line, 'DTSTART')) {
                            if (preg_match('/[:=](\d{8})/', $line, $matches)) {
                                $currentEvent['dtstart'] = $matches[1];
                            }
                        } elseif (str_starts_with($line, 'SUMMARY:')) {
                            $currentEvent['summary'] = trim(substr($line, 8));
                        } elseif (str_starts_with($line, 'DESCRIPTION:')) {
                            $currentEvent['description'] = trim(substr($line, 12));
                        }
                    }
                }

                if (empty($holidays)) {
                    Log::warning("Google Calendar ICS parsed empty holidays for year $year.");
                    return self::getFallbackHolidays($year);
                }

                return $holidays;
            } catch (\Exception $e) {
                Log::error('Failed to fetch Google Calendar holidays: ' . $e->getMessage());
                return self::getFallbackHolidays($year);
            }
        });
    }

    /**
     * Fallback holidays in case Google Calendar API is unreachable or offline
     */
    private static function getFallbackHolidays($year)
    {
        return [
            "$year-01-01" => "Tahun Baru Masehi",
            "$year-05-01" => "Hari Buruh Internasional",
            "$year-06-01" => "Hari Lahir Pancasila",
            "$year-08-17" => "Hari Kemerdekaan RI",
            "$year-12-25" => "Hari Raya Natal",
        ];
    }
}
