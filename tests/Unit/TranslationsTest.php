<?php

it('has every interface text in every language', function () {
    $dir = __DIR__.'/../../lang';
    $english = array_keys(require $dir.'/en/help.php');

    foreach (glob($dir.'/*/help.php') as $file) {
        expect(array_keys(require $file))->toEqualCanonicalizing($english, basename(dirname($file)).' is missing or has extra keys');
    }

    expect(glob($dir.'/*/help.php'))->toHaveCount(2);
});
