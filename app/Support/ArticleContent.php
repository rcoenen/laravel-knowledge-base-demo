<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

/**
 * Prepares article content for display: plain text becomes paragraphs, and
 * every <h2> gets a stable id so the page can show an "On this page" list.
 *
 * Article HTML is written by staff in the admin and rendered as-is, like the
 * rest of the knowledge base; it is not sanitised here.
 */
final class ArticleContent
{
    /**
     * @return array{html: string, toc: list<array{id: string, text: string}>}
     */
    public static function prepare(string $content): array
    {
        $content = trim($content);

        if ($content === '') {
            return ['html' => '', 'toc' => []];
        }

        if ($content === strip_tags($content)) {
            $paragraphs = preg_split('/\R{2,}/', $content) ?: [$content];

            $html = collect($paragraphs)
                ->map(fn (string $p) => '<p>'.nl2br(e(trim($p)), false).'</p>')
                ->implode("\n");

            return ['html' => $html, 'toc' => []];
        }

        if (! str_contains(strtolower($content), '<h2')) {
            return ['html' => $content, 'toc' => []];
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8"?><div id="kb-help-root">'.$content.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $toc = [];
        $used = [];

        /** @var DOMElement $heading */
        foreach ($document->getElementsByTagName('h2') as $heading) {
            $text = trim($heading->textContent);

            if ($text === '') {
                continue;
            }

            $id = $heading->getAttribute('id') ?: Str::slug($text) ?: 'section';
            $base = $id;
            $n = 2;

            while (isset($used[$id])) {
                $id = $base.'-'.$n++;
            }

            $used[$id] = true;
            $heading->setAttribute('id', $id);
            $toc[] = ['id' => $id, 'text' => $text];
        }

        $root = $document->getElementById('kb-help-root');
        $html = '';

        foreach ($root?->childNodes ?? [] as $child) {
            $html .= $document->saveHTML($child);
        }

        return ['html' => $html, 'toc' => $toc];
    }
}
