<?php

use App\Models\User;
use JeffersonGoncalves\FilamentKnowledgeBase\Admin\Resources\Articles\Pages\EditArticle;
use JeffersonGoncalves\FilamentKnowledgeBase\Admin\Resources\Articles\RelationManagers\FeedbackRelationManager;
use JeffersonGoncalves\FilamentKnowledgeBase\Admin\Resources\Articles\RelationManagers\RelatedArticlesRelationManager;
use JeffersonGoncalves\FilamentKnowledgeBase\Admin\Resources\Articles\RelationManagers\VersionsRelationManager;
use JeffersonGoncalves\KnowledgeBase\Models\Article;
use JeffersonGoncalves\KnowledgeBase\Models\Category;
use JeffersonGoncalves\KnowledgeBase\Models\Collection;
use Livewire\Livewire;

/*
 * Smoke test: every knowledge base admin screen opens without errors, for
 * every demo article, category and collection.
 */

beforeEach(function () {
    $this->seed();
});

it('fills in the demo login and lets staff in', function () {
    $this->get('/admin/login')->assertOk()->assertSee(config('demo.email'));

    expect(auth()->attempt(['email' => config('demo.email'), 'password' => config('demo.password')]))->toBeTrue();
});

it('shows the demo bar in the admin', function () {
    $this->actingAs(User::query()->firstOrFail())
        ->get('/admin')
        ->assertOk()
        ->assertSee('data-demo-bar', false);
});

it('opens every article, category and collection screen', function () {
    $this->actingAs(User::query()->firstOrFail());

    $this->get('/admin/articles')->assertOk()->assertSee('Handling a damaged-plant claim (internal)');
    $this->get('/admin/articles/create')->assertOk();

    foreach (Article::all() as $article) {
        $this->get("/admin/articles/{$article->getRouteKey()}")->assertOk();
        $this->get("/admin/articles/{$article->getRouteKey()}/edit")->assertOk();
    }

    $this->get('/admin/categories')->assertOk()->assertSee('Plant care');
    $this->get('/admin/categories/create')->assertOk();

    foreach (Category::all() as $category) {
        $this->get("/admin/categories/{$category->getRouteKey()}/edit")->assertOk();
    }

    $this->get('/admin/collections')->assertOk()->assertSee('Frequently asked questions');

    foreach (Collection::all() as $collection) {
        $this->get("/admin/collections/{$collection->getRouteKey()}/edit")->assertOk();
    }
});

it('renders the article relation tabs', function () {
    $this->actingAs(User::query()->firstOrFail());

    $article = Article::query()->where('slug', 'how-to-place-your-first-order')->firstOrFail();
    $article->feedback()->create(['is_helpful' => true, 'comment' => 'Clear and helpful', 'created_at' => now()]);

    $render = fn (string $manager) => Livewire::test($manager, ['ownerRecord' => $article, 'pageClass' => EditArticle::class])
        ->assertSuccessful();

    $render(VersionsRelationManager::class)->assertSee('Initial version');
    $render(FeedbackRelationManager::class)->assertSee('Clear and helpful');
    $render(RelatedArticlesRelationManager::class)->assertSee('When will my order arrive?');
});
