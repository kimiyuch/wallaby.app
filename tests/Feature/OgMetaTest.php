<?php

it('gives the public pages a link preview with an image that exists', function (string $path) {
    $this->get($path)
        ->assertOk()
        ->assertSee('<meta property="og:image" content="'.asset('images/og.png').'">', escape: false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', escape: false);

    expect(public_path('images/og.png'))->toBeFile();
})->with(['/', '/privacy']);

it('titles the privacy page preview as the privacy page', function () {
    $this->get('/privacy')->assertSee('<meta property="og:title" content="Privacy Policy – Wallaby">', escape: false);
});

it('only serves the image source page during local development', function () {
    // Tests run as "testing", like production not "local".
    $this->get('/og-image')->assertNotFound();
});
