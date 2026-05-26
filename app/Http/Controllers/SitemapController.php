<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Package;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap for indexable public URLs, including bookable experiences and packages.
     */
    public function __invoke(): Response
    {
        $urls = [];

        $push = function (string $loc, ?string $changefreq = 'weekly', ?string $priority = '0.8') use (&$urls): void {
            $urls[] = [
                'loc' => $loc,
                'changefreq' => $changefreq,
                'priority' => $priority,
            ];
        };

        $push(url('/'), 'daily', '1.0');
        $push(url('/experiences'), 'daily', '0.9');
        $push(url('/packages'), 'daily', '0.9');
        $push(url('/visa-services'), 'weekly', '0.85');
        $push(url('/schengen-visa'), 'weekly', '0.85');
        $push(url('/uk-visa'), 'weekly', '0.85');
        $push(url('/usa-visa'), 'weekly', '0.85');
        $push(url('/canada-visa'), 'weekly', '0.85');
        $push(url('/japan-visa'), 'weekly', '0.85');
        $push(url('/australia-visa'), 'weekly', '0.85');
        $push(url('/turkey-visa'), 'weekly', '0.85');
        $push(url('/malaysia-visa'), 'weekly', '0.85');
        $push(url('/vietnam-visa'), 'weekly', '0.85');
        $push(url('/brazil-visa'), 'weekly', '0.85');
        $push(url('/south-africa-visa'), 'weekly', '0.85');
        $push(url('/evisa-assistance'), 'weekly', '0.85');
        $push(url('/tourist-visa-assistance'), 'weekly', '0.85');
        $push(url('/about'), 'monthly', '0.6');
        $push(url('/corporate-events'), 'monthly', '0.6');
        $push(url('/contact'), 'monthly', '0.7');
        $push(url('/blog'), 'weekly', '0.7');
        $push(url('/faq'), 'monthly', '0.6');

        Experience::query()
            ->where('is_active', true)
            ->orderBy('slug')
            ->pluck('slug')
            ->each(fn (string $slug) => $push(url("/experiences/{$slug}"), 'weekly', '0.85'));

        Collection::query()
            ->orderBy('slug')
            ->pluck('slug')
            ->each(fn (string $slug) => $push(url("/collections/{$slug}"), 'weekly', '0.75'));

        Package::query()
            ->where('is_active', true)
            ->orderBy('slug')
            ->pluck('slug')
            ->each(fn (string $slug) => $push(url("/packages/{$slug}"), 'weekly', '0.85'));

        Article::query()
            ->published()
            ->orderBy('slug')
            ->pluck('slug')
            ->each(fn (string $slug) => $push(url("/blog/{$slug}"), 'monthly', '0.65'));

        BlogCategory::query()
            ->active()
            ->orderBy('slug')
            ->pluck('slug')
            ->each(fn (string $slug) => $push(url("/blog?category={$slug}"), 'monthly', '0.55'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($urls as $row) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.e($row['loc']).'</loc>'.PHP_EOL;
            if ($row['changefreq'] !== null) {
                $xml .= '    <changefreq>'.e($row['changefreq']).'</changefreq>'.PHP_EOL;
            }
            if ($row['priority'] !== null) {
                $xml .= '    <priority>'.e($row['priority']).'</priority>'.PHP_EOL;
            }
            $xml .= '  </url>'.PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
