<?php

namespace Database\Seeders;

use Composer\InstalledVersions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use JeffersonGoncalves\KnowledgeBase\Enums\AssetKind;
use JeffersonGoncalves\KnowledgeBase\Enums\TranslationStatus;
use JeffersonGoncalves\KnowledgeBase\Models\Article;
use JeffersonGoncalves\KnowledgeBase\Models\Category;
use JeffersonGoncalves\KnowledgeBase\Models\Collection;
use JeffersonGoncalves\KnowledgeBase\Services\KnowledgeBaseService;
use JeffersonGoncalves\KnowledgeBase\Support\ArticleAssets;
use JeffersonGoncalves\KnowledgeBase\Support\Locales;

/**
 * The Fernleaf help center content: a fictional houseplant shop,
 * English authoritative with German translations.
 *
 * Covers every reader-facing case: articles with and without a German version
 * (fallback), an outdated German version, nested topics, related articles, an
 * FAQ collection and one internal article that must never appear publicly.
 */
class FernleafSeeder extends Seeder
{
    private KnowledgeBaseService $kb;

    private Model $author;

    /** The demo translates into German when 'de' is configured. */
    private const TRANSLATION_LOCALE = 'de';

    private bool $translate;

    public function run(KnowledgeBaseService $kb): void
    {
        $this->kb = $kb;
        $this->author = $this->author();
        $this->translate = Locales::isConfigured(self::TRANSLATION_LOCALE);

        $start = $this->category('Getting started', 'Erste Schritte', 'Your first order, step by step.', 'Deine erste Bestellung, Schritt für Schritt.', 1);
        $care = $this->category('Plant care', 'Pflanzenpflege', 'Keeping your plants healthy at home.', 'So bleiben deine Pflanzen zu Hause gesund.', 2);
        $water = $this->category('Watering and light', 'Gießen und Licht', 'How much water and light each plant needs.', 'Wie viel Wasser und Licht jede Pflanze braucht.', 1, $care);
        $orders = $this->category('Orders and delivery', 'Bestellungen und Lieferung', 'Tracking, delivery times and returns.', 'Sendungsverfolgung, Lieferzeiten und Rücksendungen.', 3);
        $account = $this->category('Your account', 'Dein Konto', 'Signing in, your details and notifications.', 'Anmelden, deine Daten und Benachrichtigungen.', 4);
        $payments = $this->category('Payments', 'Zahlungen', 'Payment methods, invoices and refunds.', 'Zahlungsarten, Rechnungen und Erstattungen.', 5);

        $about = $this->aboutArticle($start);

        $firstOrder = $this->article($start, [
            'title' => 'How to place your first order',
            'excerpt' => 'Pick your plants, choose a pot and a delivery day, and pay. You can change the order until the evening before delivery.',
            'content' => <<<'HTML'
<p>Ordering takes a few minutes. You don't need an account to browse; we ask you to create one at checkout.</p>
<h2>Choose your plants</h2>
<p>Filter by size, light and how much care you want to give. Every plant page says how big it is on arrival and how fast it grows.</p>
<h2>Pick a pot</h2>
<p>Plants come in a plastic grow pot. Add a decorative pot if you like; we deliver the plant already in it.</p>
<h2>Choose a delivery day</h2>
<ol><li>Enter your postcode to see the available days.</li><li>Pick a day and a time slot.</li><li>Add a note for the courier if needed, for example a neighbour who can take the parcel.</li></ol>
<h2>Pay and confirm</h2>
<p>You get a confirmation email right away. Until the evening before delivery you can still change or cancel the order in your account.</p>
HTML,
        ], [
            'title' => 'So gibst du deine erste Bestellung auf',
            'excerpt' => 'Wähle deine Pflanzen, einen Topf und einen Liefertag und bezahle. Bis zum Abend vor der Lieferung kannst du die Bestellung noch ändern.',
            'content' => <<<'HTML'
<p>Bestellen dauert nur ein paar Minuten. Zum Stöbern brauchst du kein Konto; beim Bezahlen bitten wir dich, eines anzulegen.</p>
<h2>Wähle deine Pflanzen</h2>
<p>Filtere nach Größe, Licht und danach, wie viel Pflege du geben möchtest. Auf jeder Pflanzenseite steht, wie groß sie bei Lieferung ist und wie schnell sie wächst.</p>
<h2>Wähle einen Topf</h2>
<p>Pflanzen kommen in einem Kunststoff-Anzuchttopf. Wenn du möchtest, fügst du einen Übertopf hinzu; wir liefern die Pflanze dann schon darin.</p>
<h2>Wähle einen Liefertag</h2>
<ol><li>Gib deine Postleitzahl ein, um die verfügbaren Tage zu sehen.</li><li>Wähle einen Tag und ein Zeitfenster.</li><li>Füge bei Bedarf eine Notiz für den Kurier hinzu, zum Beispiel einen Nachbarn, der das Paket annehmen kann.</li></ol>
<h2>Bezahlen und bestätigen</h2>
<p>Du bekommst sofort eine Bestätigung per E-Mail. Bis zum Abend vor der Lieferung kannst du die Bestellung in deinem Konto noch ändern oder stornieren.</p>
HTML,
        ]);

        $arrival = $this->article($care, [
            'title' => 'What to do when your plant arrives',
            'excerpt' => 'Unpack it straight away, give it a spot out of direct sun for a few days, and check the soil before watering.',
            'content' => <<<'HTML'
<p>A few days in a box is a big change for a plant. These steps help it settle in.</p>
<h2>Unpack straight away</h2>
<p>Open the box on the day it arrives, even if you are busy. Remove the paper sleeve carefully by tearing it, rather than pulling it over the leaves.</p>
<h2>Find a temporary spot</h2>
<ul><li>Bright, but out of direct sun for the first few days.</li><li>Away from radiators and draughty doors.</li><li>Not right next to other plants, in case of pests.</li></ul>
<h2>Check before you water</h2>
<p>Push a finger into the soil. If the top few centimetres are dry, water until it drains from the bottom of the grow pot. If it is still damp, wait.</p>
<h2>Expect a dropped leaf or two</h2>
<p>A yellow leaf in the first week is normal. If more than a few leaves drop, send us a photo and we'll help.</p>
HTML,
        ], [
            'title' => 'Was tun, wenn deine Pflanze ankommt',
            'excerpt' => 'Pack sie sofort aus, gib ihr ein paar Tage einen Platz ohne direkte Sonne und prüfe die Erde, bevor du gießt.',
            'content' => <<<'HTML'
<p>Ein paar Tage in einem Karton sind eine große Umstellung für eine Pflanze. Mit diesen Schritten gewöhnt sie sich schnell ein.</p>
<h2>Sofort auspacken</h2>
<p>Öffne den Karton noch am Liefertag, auch wenn du viel zu tun hast. Reiß die Papierhülle vorsichtig auf, statt sie über die Blätter zu ziehen.</p>
<h2>Einen vorläufigen Platz finden</h2>
<ul><li>Hell, aber in den ersten Tagen ohne direkte Sonne.</li><li>Nicht in der Nähe von Heizkörpern und zugigen Türen.</li><li>Nicht direkt neben anderen Pflanzen, falls Schädlinge mitgekommen sind.</li></ul>
<h2>Erst prüfen, dann gießen</h2>
<p>Steck einen Finger in die Erde. Sind die obersten Zentimeter trocken, gieß, bis Wasser unten aus dem Anzuchttopf läuft. Ist sie noch feucht, warte.</p>
<h2>Ein oder zwei abgeworfene Blätter sind normal</h2>
<p>Ein gelbes Blatt in der ersten Woche ist normal. Fallen mehr als ein paar Blätter ab, schick uns ein Foto, und wir helfen dir.</p>
HTML,
        ]);

        $howOften = $this->article($water, [
            'title' => 'How often should I water my plants?',
            'excerpt' => 'There is no fixed schedule: water when the top of the soil is dry. That can be weekly in summer and every few weeks in winter.',
            'content' => <<<'HTML'
<p>Most houseplants die from too much water, not too little. Watering by the soil instead of the calendar avoids that.</p>
<h2>The finger test</h2>
<p>Push a finger a few centimetres into the soil. Dry? Water thoroughly. Damp? Check again in a few days.</p>
<h2>Seasons matter</h2>
<p>Plants grow slowly in winter and need far less water. In summer, with more light and warmth, they dry out faster.</p>
<h2>Exceptions</h2>
<ul><li>Succulents and cacti: let the soil dry out completely between waterings.</li><li>Ferns and calatheas: keep the soil lightly moist.</li></ul>
HTML,
        ]);

        $lowLight = $this->article($water, [
            'title' => 'Which plants do well in low light?',
            'excerpt' => 'Snake plants, ZZ plants and pothos cope with a darker spot. No plant grows without any light, though.',
            'content' => <<<'HTML'
<p>A spot a few metres from a window, or by a north-facing window, counts as low light.</p>
<h2>Good choices</h2>
<ul><li><strong>Snake plant</strong>: very forgiving, needs little water.</li><li><strong>ZZ plant</strong>: glossy leaves, slow and steady.</li><li><strong>Pothos</strong>: trailing, grows quickly once it settles.</li></ul>
<h2>What low light means for care</h2>
<p>Plants in low light use less water, so water less often than the plant label suggests.</p>
HTML,
        ], [
            'title' => 'Welche Pflanzen kommen mit wenig Licht aus?',
            'excerpt' => 'Bogenhanf, Glücksfeder und Efeutute vertragen einen dunkleren Platz. Ganz ohne Licht wächst aber keine Pflanze.',
            'content' => <<<'HTML'
<p>Ein Platz ein paar Meter vom Fenster entfernt oder an einem Nordfenster gilt als wenig Licht.</p>
<h2>Gute Wahl</h2>
<ul><li><strong>Bogenhanf</strong>: sehr nachsichtig, braucht wenig Wasser.</li><li><strong>Glücksfeder</strong>: glänzende Blätter, langsam, aber stetig.</li><li><strong>Efeutute</strong>: hängend, wächst schnell, sobald sie sich eingewöhnt hat.</li></ul>
<h2>Was wenig Licht für die Pflege bedeutet</h2>
<p>Pflanzen bei wenig Licht verbrauchen weniger Wasser, also gieß seltener, als das Pflanzenetikett angibt.</p>
HTML,
        ]);

        $repot = $this->article($care, [
            'title' => 'How do I repot a plant?',
            'excerpt' => 'Repot in spring, one pot size up, with fresh potting soil. Water well afterwards and keep the plant out of direct sun for a week.',
            'content' => <<<'HTML'
<p>Most plants need a bigger pot every year or two, when roots grow out of the drainage holes.</p>
<h2>When</h2>
<p>Spring is best: the plant is starting to grow and recovers quickly.</p>
<h2>How</h2>
<ol><li>Water the day before, so the root ball slides out easily.</li><li>Choose a pot one size bigger, with a drainage hole.</li><li>Add fresh potting soil, place the plant at the same depth as before and fill around it.</li><li>Water well and let it drain.</li></ol>
<h2>Afterwards</h2>
<p>Keep the plant out of direct sun for a week and don't feed it for a month; fresh soil has enough nutrients.</p>
HTML,
        ], [
            'title' => 'Wie topfe ich eine Pflanze um?',
            'excerpt' => 'Topfe im Frühjahr um, eine Topfgröße größer, mit frischer Blumenerde. Gieß danach gründlich.',
            'content' => <<<'HTML'
<p>Die meisten Pflanzen brauchen alle ein bis zwei Jahre einen größeren Topf, wenn Wurzeln aus den Abzugslöchern wachsen.</p>
<h2>Wann</h2>
<p>Das Frühjahr ist der beste Zeitpunkt: Die Pflanze beginnt zu wachsen und erholt sich schnell.</p>
<h2>Wie</h2>
<ol><li>Wähle einen Topf, der eine Größe größer ist und ein Abzugsloch hat.</li><li>Füll frische Blumenerde ein, setz die Pflanze so tief wie vorher und füll rundherum auf.</li><li>Gieß gründlich und lass das Wasser ablaufen.</li></ol>
HTML,
        ], outdated: true);

        $delivery = $this->article($orders, [
            'title' => 'When will my order arrive?',
            'excerpt' => 'On the day and time slot you chose at checkout. On the morning of delivery we email you a link to follow the courier.',
            'content' => <<<'HTML'
<p>We deliver on the day and in the time slot you chose when ordering.</p>
<h2>Following your delivery</h2>
<p>On the morning of delivery you get an email with a link to follow the courier, including a narrower arrival window.</p>
<h2>Not at home?</h2>
<p>The courier tries a neighbour first. If that's not possible, we contact you to arrange a new day at no extra cost.</p>
HTML,
        ], [
            'title' => 'Wann kommt meine Bestellung an?',
            'excerpt' => 'An dem Tag und in dem Zeitfenster, die du beim Bezahlen gewählt hast. Am Morgen der Lieferung schicken wir dir einen Link, mit dem du den Kurier verfolgen kannst.',
            'content' => <<<'HTML'
<p>Wir liefern an dem Tag und in dem Zeitfenster, die du bei der Bestellung gewählt hast.</p>
<h2>Deine Lieferung verfolgen</h2>
<p>Am Morgen der Lieferung bekommst du eine E-Mail mit einem Link, um den Kurier zu verfolgen, mit einem genaueren Zeitfenster.</p>
<h2>Nicht zu Hause?</h2>
<p>Der Kurier versucht es zuerst bei einem Nachbarn. Klappt das nicht, melden wir uns bei dir, um kostenlos einen neuen Liefertag zu vereinbaren.</p>
HTML,
        ]);

        $email = $this->article($account, [
            'title' => 'How do I change my email address?',
            'excerpt' => 'Go to Settings, choose Email, enter the new address and confirm it with the link we send there.',
            'content' => <<<'HTML'
<ol><li>Sign in and open <strong>Settings</strong>.</li><li>Choose <strong>Email</strong> and enter your new address.</li><li>Open the confirmation link we send to the new address. Until then, we keep using the old one.</li></ol>
<p>Lost access to your old address? Contact us and we'll verify your account another way.</p>
HTML,
        ], [
            'title' => 'Wie ändere ich meine E-Mail-Adresse?',
            'excerpt' => 'Geh zu Einstellungen, wähle E-Mail, gib die neue Adresse ein und bestätige sie mit dem Link, den wir dorthin schicken.',
            'content' => <<<'HTML'
<ol><li>Melde dich an und öffne <strong>Einstellungen</strong>.</li><li>Wähle <strong>E-Mail</strong> und gib deine neue Adresse ein.</li><li>Öffne den Bestätigungslink, den wir an die neue Adresse schicken. Bis dahin verwenden wir weiter die alte.</li></ol>
<p>Kein Zugriff mehr auf deine alte Adresse? Melde dich bei uns, dann prüfen wir dein Konto auf anderem Weg.</p>
HTML,
        ]);

        $paying = $this->article($payments, [
            'title' => 'Which payment methods can I use?',
            'excerpt' => 'Credit card, PayPal, Apple Pay and gift cards. You get an invoice for every order in your account.',
            'content' => <<<'HTML'
<p>You can pay with a credit card, PayPal, Apple Pay or a gift card. Gift cards can be combined with any other method.</p>
<h2>Invoices</h2>
<p>Every order comes with an invoice in your account under <strong>Orders</strong>.</p>
<h2>Refunds</h2>
<p>Refunds go back to the method you paid with, usually within five working days.</p>
HTML,
        ], [
            'title' => 'Welche Zahlungsarten kann ich nutzen?',
            'excerpt' => 'Kreditkarte, PayPal, Apple Pay und Geschenkkarten. Zu jeder Bestellung bekommst du eine Rechnung in deinem Konto.',
            'content' => <<<'HTML'
<p>Du kannst mit Kreditkarte, PayPal, Apple Pay oder einer Geschenkkarte bezahlen. Geschenkkarten lassen sich mit jeder anderen Zahlungsart kombinieren.</p>
<h2>Rechnungen</h2>
<p>Zu jeder Bestellung gehört eine Rechnung in deinem Konto unter <strong>Bestellungen</strong>.</p>
<h2>Erstattungen</h2>
<p>Erstattungen gehen auf die Zahlungsart zurück, mit der du bezahlt hast, meist innerhalb von fünf Werktagen.</p>
HTML,
        ]);

        // Staff-only: must never appear on the public pages, and neither may its image.
        $claim = $this->article($orders, [
            'title' => 'Handling a damaged-plant claim (internal)',
            'excerpt' => 'Internal procedure for support staff.',
            'content' => '<p>Internal: ask for a photo, then offer a replacement or a refund.</p>',
        ], visibility: 'internal');
        $this->attachInternalImage($claim);

        // One shared image used in English and German, one German-only image and
        // one PDF attachment, so the demo shows the whole asset flow.
        $this->attachAssets($arrival);

        $howOften->relatedArticles()->attach($lowLight->getKey(), ['sort_order' => 1]);
        $lowLight->relatedArticles()->attach($howOften->getKey(), ['sort_order' => 1]);
        $arrival->relatedArticles()->attach($howOften->getKey(), ['sort_order' => 1]);
        $arrival->relatedArticles()->attach($repot->getKey(), ['sort_order' => 2]);
        $repot->relatedArticles()->attach($arrival->getKey(), ['sort_order' => 1]);
        $firstOrder->relatedArticles()->attach($delivery->getKey(), ['sort_order' => 1]);

        // Newest first in a topic: the about article opens "Getting started".
        Article::query()->whereKeyNot($about->getKey())->update(['created_at' => now()->subDay()]);

        $faq = Collection::query()->firstOrCreate(['slug' => 'frequently-asked-questions'], [
            'name' => 'Frequently asked questions',
            'description' => 'Short answers to the questions we get most.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        if ($this->translate && ! $faq->translations()->where('locale', self::TRANSLATION_LOCALE)->exists()) {
            $faq->translations()->create([
                'locale' => self::TRANSLATION_LOCALE,
                'name' => 'Häufige Fragen',
                'slug' => 'haeufige-fragen',
                'description' => 'Kurze Antworten auf die Fragen, die wir am häufigsten bekommen.',
            ]);
        }

        foreach ([$howOften, $delivery, $arrival, $paying, $email, $repot, $firstOrder] as $article) {
            $faq->addArticle($article);
        }
    }

    /**
     * Give one article a shared image, a German-only image and a PDF, and put
     * them in the article's content: the shared image in both languages, the
     * German image and the file link only in the language that uses them.
     */
    private function attachAssets(Article $article): void
    {
        $assets = app(ArticleAssets::class);
        $directory = database_path('seeders/assets');

        $shared = $assets->store($article, $directory.'/fernleaf-plant.png', AssetKind::Image);
        $guide = $assets->store($article, $directory.'/fernleaf-care-guide.pdf', AssetKind::File);
        $german = $this->translate
            ? $assets->store($article, $directory.'/fernleaf-plant-de.png', AssetKind::Image, self::TRANSLATION_LOCALE)
            : null;

        $sharedImage = $this->imageMarkup($assets, $shared, 'A fern in a pot');
        $guideLink = '<p><a href="'.$assets->url($guide).'">'
            .$guide->getAttribute('original_name').', '
            .Number::fileSize((int) $guide->getAttribute('size')).'</a></p>';

        $english = $article->translation(Locales::default());

        if ($english instanceof Model) {
            $english->update(['content' => (string) $english->getAttribute('content').$sharedImage.$guideLink]);
        }

        if ($german instanceof Model) {
            $germanImage = $this->imageMarkup($assets, $german, 'Eine Farnpflanze im Topf');
            $translation = $article->translation(self::TRANSLATION_LOCALE);

            if ($translation instanceof Model) {
                $translation->update(['content' => (string) $translation->getAttribute('content').$sharedImage.$germanImage]);
            }
        }
    }

    /**
     * An example photo in the internal article. It is served only through
     * signed URLs, so its address works in the admin and nowhere else.
     */
    private function attachInternalImage(Article $article): void
    {
        $assets = app(ArticleAssets::class);
        $photo = $assets->store($article, database_path('seeders/assets/fernleaf-plant.png'), AssetKind::Image);
        $english = $article->translation(Locales::default());

        if ($english instanceof Model) {
            $english->update(['content' => (string) $english->getAttribute('content')
                .'<p>An example of the photo to ask for:</p>'
                .$this->imageMarkup($assets, $photo, 'Example photo of a plant')]);
        }
    }

    /**
     * An image element that refers to an asset by its id, with the current URL
     * as a cache (the public pages recompute it).
     */
    private function imageMarkup(ArticleAssets $assets, Model $asset, string $alt): string
    {
        return '<p><img data-id="'.$asset->getAttribute('uuid').'" src="'
            .$assets->url($asset).'" alt="'.$alt.'"></p>';
    }

    /**
     * "About this demo": what Fernleaf is and what to try, with links to
     * the pages that show each feature.
     */
    private function aboutArticle(Category $category): Article
    {
        $links = config('help-center.links');
        $core = $this->version('jeffersongoncalves/laravel-knowledge-base');
        $plugin = $this->version('jeffersongoncalves/filament-knowledge-base');

        return $this->article($category, [
            'title' => 'About this demo',
            'excerpt' => 'Fernleaf is a made-up plant shop. Its help center shows how Laravel Knowledge Base runs the help section of an online service; here is what to try.',
            'content' => <<<HTML
<p>Fernleaf is not a real shop: nothing is for sale and every article is made up. This help center is the demo. It shows how <a href="{$links['core']}">Laravel Knowledge Base</a> runs the help section of an online service, with its <a href="{$links['plugin']}">Filament plugin</a> as the staff admin.</p>
<h2>What to try</h2>
<ul>
<li><strong>Two languages.</strong> Switch to German with the flags at the top. English is the authoritative language; most articles also have a German version.</li>
<li><strong>Your language first.</strong> Opening the help center without a language, like <a href="/">the start page</a>, picks English or German from your browser settings and remembers the language you choose.</li>
<li><strong>A missing translation.</strong> <a href="/de/help/articles/how-often-should-i-water-my-plants">This article</a> has no German version, so German readers get the English one, with a notice.</li>
<li><strong>An outdated translation.</strong> The English version of <a href="/de/help/articles/wie-topfe-ich-eine-pflanze-um">this article</a> changed after it was translated, so the German version is flagged as possibly out of date.</li>
<li><strong>Search per language.</strong> <a href="/de/help/search?q=Lieferung">Search in German</a> finds the German versions.</li>
<li><strong>Slugs per language.</strong> An English address under /de, like <a href="/de/help/articles/when-will-my-order-arrive">this one</a>, redirects to the German address.</li>
<li><strong>An FAQ collection.</strong> The <a href="/en/help/collections/frequently-asked-questions">frequently asked questions</a> are a hand-picked, ordered list of articles from several topics.</li>
<li><strong>Internal articles.</strong> Support staff have an article on handling damaged-plant claims. Customers never see it here, not even in search, but staff do in the <a href="/admin">admin</a>. Its image is private as well: the admin shows it through an address that expires, which doesn't work for anyone else.</li>
<li><strong>Images and files.</strong> <a href="/en/help/articles/what-to-do-when-your-plant-arrives">This article</a> has a photo, a PDF care guide and, <a href="/de/help/articles/was-tun-wenn-deine-pflanze-ankommt">in German</a>, its own German picture next to the shared photo. Each article keeps its images and files in one folder, and translations reuse them without uploading again.</li>
</ul>
<h2>The admin</h2>
<p>The <a href="/admin">admin</a> is the Filament plugin. Sign in with <strong>admin@fernleaf.test</strong> and password <strong>Pl3nts&amp;P0ts!</strong> (already filled in). There you edit each language in its own tab, mark translations as outdated, set an article to internal and reorder the FAQ. In the editor, Insert image and Attach file reuse the article's images and files or upload new ones; the Assets tab shows them all, with where each one is used, and an article that still refers to a deleted image is flagged until it's fixed. View on site opens the article here in a new tab.</p>
<h2>Under the hood</h2>
<p>This demo runs on laravel-knowledge-base {$core} and filament-knowledge-base {$plugin}. The public pages are part of this demo app, not of the packages. The source is <a href="{$links['demo']}">on GitHub</a>.</p>
HTML,
        ], [
            'title' => 'Über diese Demo',
            'excerpt' => 'Fernleaf ist ein erfundener Pflanzenshop. Sein Hilfecenter zeigt, wie Laravel Knowledge Base den Hilfebereich eines Onlinedienstes betreibt; hier steht, was du ausprobieren kannst.',
            'content' => <<<HTML
<p>Fernleaf ist kein echter Shop: Hier wird nichts verkauft, und alle Artikel sind erfunden. Dieses Hilfecenter ist die Demo. Es zeigt, wie <a href="{$links['core']}">Laravel Knowledge Base</a> den Hilfebereich eines Onlinedienstes betreibt, mit seinem <a href="{$links['plugin']}">Filament-Plugin</a> als Admin für Mitarbeiter.</p>
<h2>Was du ausprobieren kannst</h2>
<ul>
<li><strong>Zwei Sprachen.</strong> Wechsle oben über die Flaggen zu Englisch. Englisch ist die maßgebliche Sprache; die meisten Artikel haben auch eine deutsche Version.</li>
<li><strong>Deine Sprache zuerst.</strong> Öffnest du das Hilfecenter ohne Sprache, wie <a href="/">die Startseite</a>, wählt es Englisch oder Deutsch nach deinen Browsereinstellungen und merkt sich die Sprache, die du wählst.</li>
<li><strong>Eine fehlende Übersetzung.</strong> <a href="/de/help/articles/how-often-should-i-water-my-plants">Dieser Artikel</a> hat keine deutsche Version. Du bekommst die englische, mit einem Hinweis.</li>
<li><strong>Eine veraltete Übersetzung.</strong> Die englische Version von <a href="/de/help/articles/wie-topfe-ich-eine-pflanze-um">diesem Artikel</a> wurde nach der Übersetzung geändert, deshalb ist die deutsche als möglicherweise veraltet markiert.</li>
<li><strong>Suche pro Sprache.</strong> <a href="/de/help/search?q=Lieferung">Eine Suche auf Deutsch</a> findet die deutschen Versionen.</li>
<li><strong>Adressen pro Sprache.</strong> Eine englische Adresse unter /de, wie <a href="/de/help/articles/when-will-my-order-arrive">diese</a>, leitet zur deutschen Adresse weiter.</li>
<li><strong>Eine FAQ-Sammlung.</strong> Die <a href="/de/help/collections/haeufige-fragen">häufigen Fragen</a> sind eine ausgewählte, sortierte Liste von Artikeln aus mehreren Themen.</li>
<li><strong>Interne Artikel.</strong> Der Support hat einen Artikel zum Umgang mit beschädigten Pflanzen. Kunden sehen ihn hier nie, auch nicht in der Suche, Mitarbeiter aber schon, im <a href="/admin">Admin</a>.</li>
<li><strong>Bilder und Dateien.</strong> <a href="/de/help/articles/was-tun-wenn-deine-pflanze-ankommt">Dieser Artikel</a> hat ein Foto, eine PDF-Pflegeanleitung und auf Deutsch ein eigenes deutsches Bild neben dem geteilten Foto. Jeder Artikel hat seine Bilder und Dateien in einem Ordner, und Übersetzungen verwenden sie wieder, ohne erneutes Hochladen.</li>
</ul>
<h2>Der Admin</h2>
<p>Der <a href="/admin">Admin</a> ist das Filament-Plugin. Melde dich mit <strong>admin@fernleaf.test</strong> und dem Passwort <strong>Pl3nts&amp;P0ts!</strong> an (schon ausgefüllt). Dort bearbeitest du jede Sprache in einem eigenen Tab, markierst Übersetzungen als veraltet, stellst einen Artikel auf intern und sortierst die FAQ. Im Editor fügen „Bild einfügen“ und „Datei anhängen“ vorhandene Bilder und Dateien des Artikels ein oder laden neue hoch; der Tab „Dateien“ zeigt alle, mit ihrer Verwendung, und ein Artikel, der noch auf ein gelöschtes Bild verweist, bleibt markiert, bis er korrigiert ist. „Auf der Website ansehen“ öffnet den Artikel hier in einem neuen Tab.</p>
<h2>Technik</h2>
<p>Diese Demo läuft mit laravel-knowledge-base {$core} und filament-knowledge-base {$plugin}. Die öffentlichen Seiten gehören zu dieser Demo-App, nicht zu den Paketen. Der Quellcode liegt <a href="{$links['demo']}">auf GitHub</a>.</p>
HTML,
        ]);
    }

    private function version(string $package): string
    {
        return InstalledVersions::isInstalled($package)
            ? (string) InstalledVersions::getPrettyVersion($package)
            : 'unknown';
    }

    /**
     * @param  array{title: string, excerpt: string, content: string}  $english
     * @param  array{title: string, excerpt: string, content: string}|null  $translation
     */
    private function article(Category $category, array $english, ?array $translation = null, bool $outdated = false, string $visibility = 'public'): Article
    {
        /** @var Article $article */
        $article = $this->kb->publishArticle($this->kb->createArticle([
            'category_id' => $category->getKey(),
            'visibility' => $visibility,
            ...$english,
        ], $this->author));

        if ($translation !== null && $this->translate) {
            $article->translations()->create([
                'locale' => self::TRANSLATION_LOCALE,
                'title' => $translation['title'],
                'slug' => Str::slug($translation['title'], language: self::TRANSLATION_LOCALE),
                'excerpt' => $translation['excerpt'],
                'content' => $translation['content'],
                'status' => TranslationStatus::Published,
            ]);

            if ($outdated) {
                $article->translation(self::TRANSLATION_LOCALE)->flagOutdated();
            }
        }

        return $article;
    }

    private function category(string $english, string $translated, string $description, string $translatedDescription, int $order, ?Category $parent = null): Category
    {
        /** @var Category $category */
        $category = $this->kb->createCategory([
            'name' => $english,
            'description' => $description,
            'parent_id' => $parent?->getKey(),
            'visibility' => 'public',
            'sort_order' => $order,
        ]);

        if ($this->translate) {
            $category->translations()->create([
                'locale' => self::TRANSLATION_LOCALE,
                'name' => $translated,
                'slug' => Str::slug($translated, language: self::TRANSLATION_LOCALE),
                'description' => $translatedDescription,
            ]);
        }

        return $category;
    }

    private function author(): Model
    {
        /** @var class-string<Model> $model */
        $model = config('auth.providers.users.model');

        return $model::query()->first() ?? $model::query()->create([
            'name' => 'Help center demo',
            'email' => 'help-demo@example.test',
            'password' => Hash::make(Str::random(32)),
        ]);
    }
}
