<?php

namespace App\Http\Controllers;

use App\Support\ArticleContent;
use App\Support\HelpRoutes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use JeffersonGoncalves\KnowledgeBase\Services\KnowledgeBaseService;
use JeffersonGoncalves\KnowledgeBase\Support\HelpCenter;
use JeffersonGoncalves\KnowledgeBase\Support\Locales;

/**
 * The public help center pages. All visibility and language rules come from the
 * core's HelpCenter read API; this controller only maps its results to views.
 */
class HelpCenterController extends Controller
{
    public function __construct(private readonly HelpCenter $help) {}

    public function entry(): RedirectResponse
    {
        $locale = Locales::isConfigured(app()->getLocale()) ? app()->getLocale() : Locales::default();

        return redirect()->route(HelpRoutes::name('home'), ['locale' => $locale]);
    }

    public function home(string $locale): View
    {
        $this->useLocale($locale);

        $faqSlug = config('help-center.faq_collection');
        $aboutSlug = config('help-center.about_article');

        return view('help.home', [
            'locale' => $locale,
            'about' => $aboutSlug ? $this->help->article((string) $aboutSlug, $locale) : null,
            'categories' => $this->help->categories($locale),
            'faq' => $faqSlug ? $this->help->collection((string) $faqSlug, $locale) : null,
            'switch' => $this->everyLanguage('home'),
            'canonical' => route(HelpRoutes::name('home'), ['locale' => $locale]),
        ]);
    }

    public function search(Request $request, string $locale): View
    {
        $this->useLocale($locale);

        $query = trim((string) $request->query('q', ''));
        $searched = mb_strlen($query) >= (int) config('help-center.search_min_length', 2);

        $results = $searched
            ? $this->searchResults($query, $locale)
            : collect();

        return view('help.search', [
            'locale' => $locale,
            'query' => $query,
            'searched' => $searched,
            'results' => $results,
            'categories' => $searched && $results->isEmpty() ? $this->help->categories($locale) : [],
            'switch' => $this->everyLanguage('search', $query !== '' ? ['q' => $query] : []),
            'canonical' => null,
        ]);
    }

    public function article(Request $request, string $locale, string $slug): View|RedirectResponse
    {
        $this->useLocale($locale);

        $page = $this->help->article($slug, $locale);

        abort_if($page === null, 404);

        // A slug from another language (or an old slug): move to the served
        // version's slug in the requested language prefix. A fallback, where
        // the served language differs from the requested one, is shown with a
        // notice instead.
        if ($page->locale === $locale && $page->redirectSlug !== null) {
            return redirect()->route(HelpRoutes::name('article'), ['locale' => $locale, 'slug' => $page->redirectSlug], 301);
        }

        $page->article->incrementViewCount();

        $switch = [];
        foreach ($page->alternates as $alternateLocale => $alternateSlug) {
            $switch[$alternateLocale] = route(HelpRoutes::name('article'), ['locale' => $alternateLocale, 'slug' => $alternateSlug]);
        }

        $translation = $page->translation;
        $authoritative = $page->article->getAttribute('source_locale');

        return view('help.article', [
            'locale' => $locale,
            'page' => $page,
            'content' => ArticleContent::prepare((string) $translation->getAttribute('content')),
            'updatedAt' => $translation->getAttribute('updated_at'),
            'isOutdated' => config('help-center.show_outdated_notice', true)
                && (bool) $translation->getAttribute('is_outdated'),
            'authoritativeUrl' => $authoritative && isset($page->alternates[$authoritative]) && $authoritative !== $page->locale
                ? $switch[$authoritative]
                : null,
            'authoritativeLocale' => $authoritative,
            'feedbackGiven' => (bool) $request->session()->get($this->feedbackKey($page->article->getKey())),
            'switch' => $switch,
            'canonical' => route(HelpRoutes::name('article'), ['locale' => $page->locale, 'slug' => $page->slug]),
        ]);
    }

    public function feedback(Request $request, KnowledgeBaseService $kb, string $locale, string $slug): RedirectResponse
    {
        $this->useLocale($locale);

        abort_unless(config('help-center.feedback', true) && config('knowledge-base.feedback_enabled', true), 404);

        $page = $this->help->article($slug, $locale);

        abort_if($page === null, 404);

        $key = $this->feedbackKey($page->article->getKey());

        if (! $request->session()->get($key)) {
            $kb->addFeedback($page->article, $request->boolean('helpful'), $request->user(), null, $request->ip());
            $request->session()->put($key, true);
        }

        return redirect()->to(route(HelpRoutes::name('article'), ['locale' => $locale, 'slug' => $slug]).'#feedback');
    }

    public function category(string $locale, string $slug): View|RedirectResponse
    {
        $this->useLocale($locale);

        $page = $this->help->category($slug, $locale);

        abort_if($page === null, 404);

        if ($page->locale === $locale && $page->redirectSlug !== null) {
            return redirect()->route(HelpRoutes::name('category'), ['locale' => $locale, 'slug' => $page->redirectSlug], 301);
        }

        return view('help.category', [
            'locale' => $locale,
            'page' => $page,
            'parent' => HelpRoutes::parentOf($this->help->categories($locale), $page->category->getKey()),
            'switch' => $this->sameSlugInEveryLanguage($page->slug, 'category'),
            'canonical' => route(HelpRoutes::name('category'), ['locale' => $page->locale, 'slug' => $page->slug]),
        ]);
    }

    public function collection(string $locale, string $slug): View|RedirectResponse
    {
        $this->useLocale($locale);

        $page = $this->help->collection($slug, $locale);

        abort_if($page === null, 404);

        if ($page->locale === $locale && $page->redirectSlug !== null) {
            return redirect()->route(HelpRoutes::name('collection'), ['locale' => $locale, 'slug' => $page->redirectSlug], 301);
        }

        return view('help.collection', [
            'locale' => $locale,
            'page' => $page,
            'switch' => $this->sameSlugInEveryLanguage($page->slug, 'collection'),
            'canonical' => route(HelpRoutes::name('collection'), ['locale' => $page->locale, 'slug' => $page->slug]),
        ]);
    }

    /**
     * Search results with the version the reader is served: the core returns
     * each article with `served_locale` and, since it carries it, the matching
     * version in `served_translation` (looked up here for older cores).
     *
     * @return Collection<int, array{title: string, slug: string, excerpt: ?string, locale: string, category: ?string}>
     */
    private function searchResults(string $query, string $locale): Collection
    {
        $articles = $this->help->search($query, $locale, (int) config('help-center.search_limit', 20));

        $articles->loadMissing(['category.translations']);

        return $articles->map(function ($article) use ($locale) {
            $served = (string) ($article->getAttribute('served_locale') ?? $locale);
            $translation = property_exists($article, 'served_translation') && $article->served_translation !== null
                ? $article->served_translation
                : $article->translations()->where('locale', $served)->first();
            $category = $article->category;
            $categoryName = $category?->translations?->firstWhere('locale', $locale)?->getAttribute('name')
                ?? $category?->getAttribute('name');

            return [
                'title' => (string) ($translation?->getAttribute('title') ?? $article->getAttribute('title')),
                'slug' => (string) ($translation?->getAttribute('slug') ?? $article->getAttribute('slug')),
                'excerpt' => $translation?->getAttribute('excerpt') ?? $article->getAttribute('excerpt'),
                'locale' => $served,
                'category' => $categoryName,
            ];
        })->values();
    }

    private function useLocale(string $locale): void
    {
        abort_unless(Locales::isConfigured($locale), 404);

        app()->setLocale($locale);
    }

    /**
     * The same page in every configured language (pages that exist in all of
     * them: home, search).
     *
     * @param  array<string, string>  $parameters
     * @return array<string, string>
     */
    private function everyLanguage(string $route, array $parameters = []): array
    {
        $switch = [];

        foreach (Locales::all() as $candidate) {
            $switch[$candidate] = route(HelpRoutes::name($route), [...$parameters, 'locale' => $candidate]);
        }

        return $switch;
    }

    /**
     * A category or collection in every language it really exists in (its own
     * version, not a fallback).
     *
     * @return array<string, string>
     */
    private function sameSlugInEveryLanguage(string $slug, string $route): array
    {
        $switch = [];

        foreach (Locales::all() as $candidate) {
            $page = $route === 'category'
                ? $this->help->category($slug, $candidate)
                : $this->help->collection($slug, $candidate);

            if ($page !== null && $page->locale === $candidate) {
                $switch[$candidate] = route(HelpRoutes::name($route), ['locale' => $candidate, 'slug' => $page->slug]);
            }
        }

        return $switch;
    }

    private function feedbackKey(mixed $articleId): string
    {
        return 'help.feedback.'.$articleId;
    }
}
