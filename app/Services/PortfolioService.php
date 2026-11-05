<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Cache;
use App\Core\CSRF;
use App\Models\Category;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Testimonial;

final class PortfolioService
{
    public static function installed(): bool
    {
        try {
            return Profile::get() !== null;
        } catch (\Throwable) {
            return false;
        }
    }

    /** @return array<string, mixed> */
    public static function compose(string $active): array
    {
        /** @var array<string, mixed> $bundle */
        $bundle = Cache::remember('public.bundle', static function (): array {
            return [
                'profile' => Profile::get(),
                'socials' => SocialLink::visible(),
                'services' => Service::visible(),
                'experiences' => Experience::visible(),
                'education' => Education::visible(),
                'skillGroups' => Skill::grouped(),
                'categories' => Category::allRows(),
                'projects' => Project::published(),
                'testimonials' => Testimonial::visible(),
                'settings' => Setting::all(),
            ];
        });
        $settings = is_array($bundle['settings'] ?? null) ? $bundle['settings'] : [];
        $start = (int) ($settings['career_start_year'] ?? 2013);
        $projects = is_array($bundle['projects'] ?? null) ? $bundle['projects'] : [];
        $bundle['stats'] = [
            'years' => max(0, (int) date('Y') - $start),
            'projects' => count($projects),
            'remedy' => (int) ($settings['incident_remedy_percent'] ?? 90),
        ];
        $bundle['active'] = $active;
        $bundle['csrf'] = CSRF::token();
        return $bundle;
    }
}
