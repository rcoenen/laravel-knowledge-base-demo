<?php

use Composer\InstalledVersions;
use JeffersonGoncalves\KnowledgeBase\Models\Article;

beforeEach(fn () => $this->seed());

it('opens the help center from / and /help', function () {
    $this->get('/')->assertRedirect('/en/help');
    $this->get('/help')->assertRedirect('/en/help');
});

it('shows the home page with quick answers and topics', function () {
    $this->get('/en/help')
        ->assertOk()
        ->assertSee('What do you need help with?')
        ->assertSee('How often should I water my plants?')
        ->assertSee('Plant care')
        ->assertSee('4 articles') // topic total includes its subtopic
        ->assertDontSee('Handling a damaged-plant claim');
});

it('shows German titles on the German home page and labels English-only answers', function () {
    $this->get('/de/help')
        ->assertOk()
        ->assertSee('Wobei können wir dir helfen?')
        ->assertSee('Wann kommt meine Bestellung an?')
        ->assertSee('How often should I water my plants?')
        ->assertSee('Auf Englisch');
});

it('returns 404 for a language that is not configured', function () {
    $this->get('/it/help')->assertNotFound();
});

it('shows an article with its table of contents, related articles and language links', function () {
    $this->get('/en/help/articles/what-to-do-when-your-plant-arrives')
        ->assertOk()
        ->assertSee('Find a temporary spot')
        ->assertSee('href="#find-a-temporary-spot"', false)
        ->assertSee('How do I repot a plant?')
        ->assertSee('hreflang="de"', false)
        ->assertSee('rel="canonical"', false);
});

it('tells German readers when they get the English version', function () {
    $this->get('/de/help/articles/how-often-should-i-water-my-plants')
        ->assertOk()
        ->assertSee('data-fallback-notice', false)
        ->assertSee('Du liest die Version auf Englisch.');
});

it('warns when the German version is outdated and links to the English one', function () {
    $this->get('/de/help/articles/wie-topfe-ich-eine-pflanze-um')
        ->assertOk()
        ->assertSee('data-outdated-notice', false)
        ->assertSee('/en/help/articles/how-do-i-repot-a-plant', false);
});

it('redirects an English slug to the German version', function () {
    $this->get('/de/help/articles/when-will-my-order-arrive')
        ->assertRedirect('/de/help/articles/wann-kommt-meine-bestellung-an')
        ->assertStatus(301);
});

it('never shows internal articles', function () {
    $this->get('/en/help/articles/handling-a-damaged-plant-claim-internal')->assertNotFound();
    $this->get('/en/help/search?q=claim')->assertDontSee('Handling a damaged-plant claim');
});

it('shows topic and collection pages', function () {
    $this->get('/de/help/categories/pflanzenpflege')
        ->assertOk()
        ->assertSee('Gießen und Licht')
        ->assertSee('Wie topfe ich eine Pflanze um?');

    $this->get('/de/help/collections/haeufige-fragen')
        ->assertOk()
        ->assertSee('Häufige Fragen')
        ->assertSee('Wie ändere ich meine E-Mail-Adresse?');
});

it('shows search results in the reader\'s language', function () {
    $this->get('/de/help/search?q=Lieferung')
        ->assertOk()
        ->assertSee('Wann kommt meine Bestellung an?')
        ->assertSee('data-served-locale="de"', false);
});

it('guides the reader when a search is too short or finds nothing', function () {
    $this->get('/en/help/search?q=a')->assertOk()->assertSee('Type at least 2 characters');

    $this->get('/en/help/search?q=telescope')
        ->assertOk()
        ->assertSee('No articles match “telescope”.')
        ->assertSee('Getting started');
});

it('records "was this helpful?" once per reader', function () {
    $article = Article::query()->where('slug', 'how-do-i-change-my-email-address')->firstOrFail();
    $url = '/en/help/articles/how-do-i-change-my-email-address';

    $this->post($url.'/feedback', ['helpful' => '1'])->assertRedirect($url.'#feedback');
    $this->post($url.'/feedback', ['helpful' => '1']);

    expect($article->fresh()->helpful_count)->toBe(1);

    $this->get($url)->assertSee('Thanks for letting us know.');
});

it('shows the Fernleaf logo before the brand name', function () {
    $this->get('/en/help')->assertOk()->assertSee('data-logo', false)->assertSee('Fernleaf');
});

it('credits Laravel Knowledge Base in the footer, opening in a new tab', function () {
    $this->get('/en/help')
        ->assertOk()
        ->assertSee('Powered by')
        ->assertSee('href="'.config('help-center.links.core').'" target="_blank" rel="noopener"', false);

    $this->get('/de/help')->assertOk()->assertSee('Betrieben mit');
});

it('says on every page that this is a demo', function () {
    $this->get('/en/help')->assertOk()->assertSee('data-demo-bar', false)->assertSee('Fernleaf is not a real shop.');
    $this->get('/de/help/articles/wann-kommt-meine-bestellung-an')->assertOk()->assertSee('Fernleaf ist kein echter Shop.');
});

it('introduces the about article at the top of the home page, in the reader\'s language', function () {
    $this->get('/en/help')
        ->assertOk()
        ->assertSee('data-about', false)
        ->assertSee('/en/help/articles/about-this-demo', false);

    $this->get('/de/help')->assertOk()->assertSee('/de/help/articles/ueber-diese-demo', false);
});

it('explains the demo and names the package versions it runs on', function () {
    $this->get('/en/help/articles/about-this-demo')
        ->assertOk()
        ->assertSee('Fernleaf is not a real shop')
        ->assertSee(config('demo.email'))
        ->assertSee('laravel-knowledge-base '.InstalledVersions::getPrettyVersion('jeffersongoncalves/laravel-knowledge-base'))
        ->assertSee('filament-knowledge-base '.InstalledVersions::getPrettyVersion('jeffersongoncalves/filament-knowledge-base'));

    $this->get('/de/help/articles/ueber-diese-demo')->assertOk()->assertSee('Über diese Demo');
});

it('lists the about article first in Getting started', function () {
    $this->get('/en/help/categories/getting-started')
        ->assertOk()
        ->assertSeeInOrder(['About this demo', 'How to place your first order']);
});

it('has working links in the about article', function () {
    $content = Article::query()->where('slug', 'about-this-demo')->firstOrFail()->content;
    preg_match_all('#href="(/[^"]+)"#', $content, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach (array_unique($matches[1]) as $path) {
        $status = $this->get($path)->status();
        expect($status === 200 || ($status >= 300 && $status < 400))->toBeTrue("{$path} returned {$status}");
    }
});

it('offers a back button one level up on every inner page', function () {
    // Article in a subtopic: back to that subtopic.
    $this->get('/en/help/articles/how-often-should-i-water-my-plants')
        ->assertOk()
        ->assertSee('data-back', false)
        ->assertSee('Back to Watering and light')
        ->assertSee('/en/help/categories/watering-and-light', false);

    // Subtopic: back to its parent topic.
    $this->get('/en/help/categories/watering-and-light')
        ->assertOk()
        ->assertSee('Back to Plant care');

    // Top-level topic, the FAQ and search: back to the help home.
    $this->get('/en/help/categories/plant-care')->assertOk()->assertSee('Back to Help');
    $this->get('/en/help/collections/frequently-asked-questions')->assertOk()->assertSee('Back to Help');
    $this->get('/en/help/search?q=water')->assertOk()->assertSee('Back to Help');

    // In the reader's language.
    $this->get('/de/help/categories/giessen-und-licht')->assertOk()->assertSee('Zurück: Pflanzenpflege');
});

it('shows a compact search in the header on inner pages only', function () {
    $this->get('/en/help')->assertOk()->assertDontSee('data-header-search', false);
    $this->get('/en/help/search?q=water')->assertOk()->assertDontSee('data-header-search', false);
    $this->get('/en/help/categories/plant-care')->assertOk()->assertSee('data-header-search', false);
});

it('describes each topic on the home page', function () {
    $this->get('/en/help')
        ->assertOk()
        ->assertSee('Keeping your plants healthy at home.')
        ->assertSee('kb-chips--small', false);
});

it('shows a flag per language in the switcher, with the language name for screen readers', function () {
    $this->get('/en/help')
        ->assertOk()
        ->assertSee('class="kb-flag"', false)
        ->assertSee('<span class="kb-visually-hidden">English</span>', false)
        ->assertSee('<span class="kb-visually-hidden">Deutsch</span>', false)
        ->assertDontSee('>EN</span>', false);
});
