<?php

use Illuminate\Support\Facades\Storage;
use JeffersonGoncalves\KnowledgeBase\Models\Article;
use JeffersonGoncalves\KnowledgeBase\Models\ArticleAsset;
use JeffersonGoncalves\KnowledgeBase\Support\ArticleAssets;

beforeEach(fn () => $this->seed());

function arrivalArticle(): Article
{
    /** @var Article $article */
    $article = Article::query()->where('slug', 'what-to-do-when-your-plant-arrives')->firstOrFail();

    return $article;
}

function arrivalAsset(Article $article, string $kind, ?string $locale = null): ArticleAsset
{
    /** @var ArticleAsset $asset */
    $asset = $article->assets()->where('kind', $kind)->where('locale', $locale)->firstOrFail();

    return $asset;
}

it('shows the shared image and the German image on the German page', function () {
    $article = arrivalArticle();
    $shared = arrivalAsset($article, 'image');
    $german = arrivalAsset($article, 'image', 'de');

    $response = $this->get('/de/help/articles/was-tun-wenn-deine-pflanze-ankommt');

    $response->assertOk()
        ->assertSee('data-id="'.$shared->getAttribute('uuid').'"', false)
        ->assertSee('data-id="'.$german->getAttribute('uuid').'"', false)
        ->assertSee('/knowledge-base/articles/'.$article->uuid.'/shared/', false)
        ->assertSee('/knowledge-base/articles/'.$article->uuid.'/de/', false);
});

it('builds asset URLs at request time, so a moved site keeps working', function () {
    config(['filesystems.disks.public.url' => 'https://moved.test/storage']);
    Storage::forgetDisk('public');

    $this->get('/de/help/articles/was-tun-wenn-deine-pflanze-ankommt')
        ->assertOk()
        ->assertSee('https://moved.test/storage/knowledge-base/articles/', false);
});

it('never shows an image that was deleted', function () {
    $article = arrivalArticle();
    $german = arrivalAsset($article, 'image', 'de');
    $uuid = (string) $german->getAttribute('uuid');

    app(ArticleAssets::class)->delete($german);

    $this->get('/de/help/articles/was-tun-wenn-deine-pflanze-ankommt')
        ->assertOk()
        ->assertDontSee('data-id="'.$uuid.'"', false)
        ->assertSee('Was tun, wenn deine Pflanze ankommt');
});

it('shows a deleted file link as plain text', function () {
    $article = arrivalArticle();
    $guide = arrivalAsset($article, 'file');
    $uuid = (string) $guide->getAttribute('uuid');
    $name = (string) $guide->getAttribute('original_name');

    app(ArticleAssets::class)->delete($guide);

    $this->get('/en/help/articles/what-to-do-when-your-plant-arrives')
        ->assertOk()
        ->assertDontSee('data-kb-asset="'.$uuid.'"', false)
        ->assertDontSee('/knowledge-base/articles/'.$article->uuid.'/shared/fernleaf-care-guide', false)
        ->assertSee($name);
});
