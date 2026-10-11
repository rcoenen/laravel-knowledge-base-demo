<?php

use App\Http\Controllers\HelpCenterController;
use Illuminate\Support\Facades\Route;
use JeffersonGoncalves\KnowledgeBase\Support\Locales;

$prefix = trim((string) config('help-center.prefix', 'help'), '/');

// The shop has no other pages: the front door is the help center.
Route::get('/', [HelpCenterController::class, 'entry']);

Route::name('help.')->group(function () use ($prefix) {
    // /help without a language: send readers to the app's current language.
    Route::get($prefix, [HelpCenterController::class, 'entry'])->name('entry');

    Route::prefix('{locale}/'.$prefix)
        ->whereIn('locale', Locales::all())
        ->group(function () {
            Route::get('/', [HelpCenterController::class, 'home'])->name('home');
            Route::get('/search', [HelpCenterController::class, 'search'])->name('search');
            Route::get('/articles/{slug}', [HelpCenterController::class, 'article'])->name('article');
            Route::post('/articles/{slug}/feedback', [HelpCenterController::class, 'feedback'])
                ->middleware('throttle:10,1')
                ->name('feedback');
            Route::get('/categories/{slug}', [HelpCenterController::class, 'category'])->name('category');
            Route::get('/collections/{slug}', [HelpCenterController::class, 'collection'])->name('collection');
        });
});
