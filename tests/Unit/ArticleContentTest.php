<?php

use App\Support\ArticleContent;

it('turns plain text into escaped paragraphs', function () {
    $result = ArticleContent::prepare("First line\nsecond line\n\nTerms & conditions apply");

    expect($result['html'])->toBe("<p>First line<br>\nsecond line</p>\n<p>Terms &amp; conditions apply</p>")
        ->and($result['toc'])->toBe([]);
});

it('gives every h2 a unique id and lists it', function () {
    $result = ArticleContent::prepare('<p>Intro</p><h2>Fees</h2><p>a</p><h2>Fees</h2><h2 id="custom">Other</h2>');

    expect($result['toc'])->toBe([
        ['id' => 'fees', 'text' => 'Fees'],
        ['id' => 'fees-2', 'text' => 'Fees'],
        ['id' => 'custom', 'text' => 'Other'],
    ])->and($result['html'])->toContain('<h2 id="fees-2">Fees</h2>');
});

it('keeps non-ASCII text intact', function () {
    $result = ArticleContent::prepare('<h2>Beëdigd vertaler</h2><p>één</p>');

    expect($result['html'])->toContain('Beëdigd vertaler')->toContain('één')
        ->and($result['toc'][0]['id'])->toBe('beedigd-vertaler');
});

it('leaves HTML without h2 headings untouched', function () {
    expect(ArticleContent::prepare('<p>Just <strong>one</strong> paragraph.</p>')['html'])
        ->toBe('<p>Just <strong>one</strong> paragraph.</p>');
});
