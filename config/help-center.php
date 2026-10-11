<?php

/*
|--------------------------------------------------------------------------
| The Fernleaf help center
|--------------------------------------------------------------------------
|
| Settings for the public help pages at /{locale}/help. The languages come
| from knowledge-base.locales; visibility, translations and search come from
| the knowledge base core.
|
*/

return [

    'prefix' => 'help',

    'brand' => 'Fernleaf',

    // The logo before the brand name: an image URL (shown as is, also used
    // as the browser tab icon), or a path to an .svg file that is inlined
    // and drawn on an accent-coloured circle.
    'logo' => '/images/fernleaf-logo.svg',

    // The flag shown for each language in the language switcher (an .svg in
    // resources/images/flags, from flag-icons). A language is not a country:
    // pick the flag your readers expect.
    'flags' => [
        'en' => 'gb',
        'de' => 'de',
    ],

    // Slug (in the default language) of the collection shown as quick answers
    // on the home page.
    'faq_collection' => 'frequently-asked-questions',

    // Slug (in the default language) of the article introduced at the top of
    // the home page.
    'about_article' => 'about-this-demo',

    // Tell readers when the version they read was flagged outdated.
    'show_outdated_notice' => true,

    // "Was this helpful?" under every article (uses the core's feedback).
    'feedback' => true,

    'search_min_length' => 2,

    'search_limit' => 20,

    // Where the demo bar and the about article point to.
    'links' => [
        'demo' => 'https://github.com/rcoenen/laravel-knowledge-base-demo',
        'core' => 'https://github.com/jeffersongoncalves/laravel-knowledge-base',
        'plugin' => 'https://github.com/jeffersongoncalves/filament-knowledge-base',
    ],

];
