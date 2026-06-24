<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Package;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private const SITEMAPS = [
        'pages' => 'pages-sitemap.xml',
        'experiences' => 'experiences-sitemap.xml',
        'packages' => 'packages-sitemap.xml',
        'visa' => 'visa-sitemap.xml',
        'blog' => 'blog-sitemap.xml',
        'collections' => 'collections-sitemap.xml',
    ];

    public function __invoke(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach (self::SITEMAPS as $section => $path) {
            $xml .= '  <sitemap>'.PHP_EOL;
            $xml .= '    <loc>'.e(url($path)).'</loc>'.PHP_EOL;
            $xml .= '    <lastmod>'.$this->sectionLastModified($section)->toAtomString().'</lastmod>'.PHP_EOL;
            $xml .= '  </sitemap>'.PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        return $this->xml($xml);
    }

    public function section(string $section): Response
    {
        abort_unless(array_key_exists($section, self::SITEMAPS), 404);

        return $this->xml($this->urlset($this->urlsForSection($section)));
    }

    protected function urlsForSection(string $section): array
    {
        return match ($section) {
            'pages' => $this->pageUrls(),
            'experiences' => $this->experienceUrls(),
            'packages' => $this->packageUrls(),
            'visa' => $this->visaUrls(),
            'blog' => $this->blogUrls(),
            'collections' => $this->collectionUrls(),
            default => [],
        };
    }

    protected function pageUrls(): array
    {
        $lastmod = $this->staticLastModified([
            app_path('Http/Controllers/PageController.php'),
            resource_path('js/Pages/Home.vue'),
            resource_path('js/Pages/About.vue'),
            resource_path('js/Pages/BusTour.vue'),
            resource_path('js/Pages/TourgratPartner.vue'),
        ]);

        return [
            $this->row('/', $lastmod, 'daily', '1.0'),
            $this->row('/about', $lastmod, 'monthly', '0.7'),
            $this->row('/earn-with-tourgrat', $lastmod, 'monthly', '0.65'),
            $this->row('/luxury-bus-tour-dubai', $lastmod, 'weekly', '0.75'),
            $this->row('/corporate-travel-event-planning-dubai', $lastmod, 'monthly', '0.55'),
            $this->row('/contact', $lastmod, 'monthly', '0.7'),
            $this->row('/faq', $lastmod, 'monthly', '0.55'),
            $this->row('/cancellation-policy', $lastmod, 'yearly', '0.35'),
            $this->row('/terms-and-conditions', $lastmod, 'yearly', '0.35'),
            $this->row('/privacy-policy', $lastmod, 'yearly', '0.35'),
        ];
    }

    protected function experienceUrls(): array
    {
        $urls = [
            $this->row('/dubai-tours-and-tickets', $this->sectionLastModified('experiences'), 'daily', '0.9'),
        ];

        foreach (Experience::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['slug', 'updated_at']) as $experience) {
            $urls[] = $this->row(
                "/experiences/{$experience->slug}",
                $experience->updated_at,
                'weekly',
                '0.85',
            );
        }

        foreach (Tour::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['slug', 'updated_at']) as $tour) {
            $urls[] = $this->row(
                "/tours/{$tour->slug}",
                $tour->updated_at,
                'weekly',
                '0.8',
            );
        }

        return $urls;
    }

    protected function packageUrls(): array
    {
        $urls = [
            $this->row('/dubai-holiday-packages', $this->sectionLastModified('packages'), 'daily', '0.9'),
        ];

        foreach (Package::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['slug', 'updated_at']) as $package) {
            $urls[] = $this->row(
                "/packages/{$package->slug}",
                $package->updated_at,
                'weekly',
                '0.85',
            );
        }

        return $urls;
    }

    protected function visaUrls(): array
    {
        $lastmod = $this->staticLastModified([
            app_path('Http/Controllers/PageController.php'),
            resource_path('js/Pages/VisaServices.vue'),
            resource_path('js/Pages/VisaLanding.vue'),
            resource_path('js/Pages/VisaProduct.vue'),
        ]);

        return collect([
            '/tourist-visa-assistance-uae-residents',
            '/schengen-visa',
            '/uk-visa',
            '/usa-visa',
            '/canada-visa',
            '/japan-visa',
            '/australia-visa',
            '/turkey-visa',
            '/malaysia-visa',
            '/vietnam-visa',
            '/brazil-visa',
            '/south-africa-visa',
            '/evisa-assistance',
            '/tourist-visa-assistance',
        ])
            ->map(fn (string $path) => $this->row($path, $lastmod, 'weekly', $path === '/tourist-visa-assistance-uae-residents' ? '0.85' : '0.75'))
            ->all();
    }

    protected function blogUrls(): array
    {
        $urls = [
            $this->row('/blog', $this->sectionLastModified('blog'), 'weekly', '0.7'),
        ];

        foreach (Article::query()
            ->published()
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['slug', 'published_at', 'updated_at']) as $article) {
            $urls[] = $this->row(
                "/blog/{$article->slug}",
                $article->updated_at ?: $article->published_at,
                'monthly',
                '0.65',
            );
        }

        return $urls;
    }

    protected function collectionUrls(): array
    {
        $urls = [];

        foreach (Collection::query()
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['slug', 'updated_at']) as $collection) {
            $urls[] = $this->row(
                "/collections/{$collection->slug}",
                $collection->updated_at,
                'weekly',
                '0.65',
            );
        }

        return $urls;
    }

    protected function sectionLastModified(string $section): Carbon
    {
        return match ($section) {
            'pages' => $this->staticLastModified([
                app_path('Http/Controllers/PageController.php'),
                resource_path('js/Pages/Home.vue'),
                resource_path('js/Pages/About.vue'),
                resource_path('js/Pages/BusTour.vue'),
                resource_path('js/Pages/TourgratPartner.vue'),
            ]),
            'experiences' => $this->latestTimestampFromQueries([
                Experience::query()->where('is_active', true),
                Tour::query()->where('is_active', true),
            ])
                ?? $this->staticLastModified([resource_path('js/Pages/Experiences/Index.vue')]),
            'packages' => $this->latestTimestampFromQueries([
                Package::query()->where('is_active', true),
            ])
                ?? $this->staticLastModified([resource_path('js/Pages/Packages/Index.vue')]),
            'visa' => $this->staticLastModified([
                app_path('Http/Controllers/PageController.php'),
                resource_path('js/Pages/VisaServices.vue'),
                resource_path('js/Pages/VisaLanding.vue'),
                resource_path('js/Pages/VisaProduct.vue'),
            ]),
            'blog' => $this->latestTimestampFromQueries([
                Article::query()->published(),
            ])
                ?? $this->staticLastModified([resource_path('js/Pages/Journal/Index.vue'), resource_path('js/Pages/Journal/Show.vue')]),
            'collections' => $this->latestTimestampFromQueries([
                Collection::query()->whereNotNull('slug'),
            ])
                ?? $this->staticLastModified([resource_path('js/Pages/Collections/Show.vue')]),
            default => now(),
        };
    }

    protected function latestTimestampFromQueries(array $queries): ?Carbon
    {
        return collect($queries)
            ->map(function ($query): ?Carbon {
                $timestamp = $query->max('updated_at');

                return $timestamp ? Carbon::parse($timestamp) : null;
            })
            ->filter()
            ->sort()
            ->last();
    }

    protected function staticLastModified(array $paths): Carbon
    {
        $timestamp = collect($paths)
            ->filter(fn (string $path) => is_file($path))
            ->map(fn (string $path) => filemtime($path))
            ->filter()
            ->max();

        return $timestamp ? Carbon::createFromTimestamp($timestamp) : now();
    }

    protected function row(string $path, Carbon|string|null $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => url($path),
            'lastmod' => $lastmod ? Carbon::parse($lastmod)->toAtomString() : now()->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    protected function urlset(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach (collect($urls)->unique('loc')->values() as $row) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.e($row['loc']).'</loc>'.PHP_EOL;
            $xml .= '    <lastmod>'.e($row['lastmod']).'</lastmod>'.PHP_EOL;
            $xml .= '    <changefreq>'.e($row['changefreq']).'</changefreq>'.PHP_EOL;
            $xml .= '    <priority>'.e($row['priority']).'</priority>'.PHP_EOL;
            $xml .= '  </url>'.PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    protected function xml(string $xml): Response
    {
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
