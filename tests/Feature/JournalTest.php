<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class JournalTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_journal_index_page_uses_database_content(): void
    {
        $response = $this->get('/journal');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Journal/Index')
            ->where('seo.title', 'Journal')
            ->has('articles', 3)
            ->where('articles.0.slug', 'choose-premium-desert-safari-dubai')
        );
    }

    public function test_journal_article_detail_page_renders(): void
    {
        $article = Article::query()->where('slug', 'private-yacht-charter-dubai-guide')->firstOrFail();

        $response = $this->get("/journal/{$article->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Journal/Show')
            ->where('seo.title', $article->seo_title)
            ->where('article.title', $article->title)
            ->has('relatedArticles')
        );
    }
}
