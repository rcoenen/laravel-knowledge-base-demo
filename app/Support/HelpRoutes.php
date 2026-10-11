<?php

namespace App\Support;

use JeffersonGoncalves\KnowledgeBase\Support\CategoryNode;
use JeffersonGoncalves\KnowledgeBase\Support\Locales;

/**
 * Route names and URLs for the help center. Views use the short helpers
 * via `HelpRoutes::url()`.
 */
final class HelpRoutes
{
    public static function name(string $route): string
    {
        return 'help.'.$route;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function url(string $route, string $locale, array $parameters = []): string
    {
        return route(self::name($route), [...$parameters, 'locale' => $locale]);
    }

    /**
     * The name of a language in that language ("Deutsch", "English"),
     * falling back to the uppercased code when ext-intl is missing.
     */
    public static function languageName(string $locale, ?string $in = null): string
    {
        if (class_exists(\Locale::class)) {
            $name = \Locale::getDisplayLanguage($locale, $in ?? $locale);

            if ($name !== '' && $name !== $locale) {
                return mb_convert_case(mb_substr($name, 0, 1), MB_CASE_UPPER).mb_substr($name, 1);
            }
        }

        return strtoupper($locale);
    }

    /**
     * The flag for a language as inline SVG (see help-center.flags), or null
     * when none is configured.
     */
    public static function flag(string $locale): ?string
    {
        $code = config('help-center.flags.'.$locale);
        $path = is_string($code) ? resource_path('images/flags/'.basename($code).'.svg') : null;

        return $path !== null && is_file($path) ? (string) file_get_contents($path) : null;
    }

    /**
     * The parent of a topic in the public topic tree, or null for a top-level
     * topic (used for the topic page's back link).
     *
     * @param  list<CategoryNode>  $nodes
     */
    public static function parentOf(array $nodes, mixed $categoryId, ?CategoryNode $parent = null): ?CategoryNode
    {
        foreach ($nodes as $node) {
            if ($node->category->getKey() == $categoryId) {
                return $parent;
            }

            if ($node->children !== [] && ($found = self::parentOf($node->children, $categoryId, $node)) !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Public articles in a topic including all its subtopics, so a topic whose
     * articles all sit in subtopics does not read as nearly empty.
     */
    public static function totalArticles(CategoryNode $node): int
    {
        $total = $node->articlesCount;

        foreach ($node->children as $child) {
            $total += self::totalArticles($child);
        }

        return $total;
    }

    /**
     * @return list<string>
     */
    public static function locales(): array
    {
        return Locales::all();
    }
}
