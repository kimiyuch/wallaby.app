@props([
    'title' => 'Wallaby – Split expenses, not relationships.',
    'description' => 'Wallaby keeps a running tab for couples and housemates. Add what you paid, see who owes whom, and settle up once a month.',
])

{{-- Link previews (Open Graph, X). The image comes from resources/views/og-image.blade.php, see scripts/og-image.sh. --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="Wallaby">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('images/og.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="The Wallaby app icon, a wallaby peeking over a mint wallet, next to the words: Split expenses, not relationships.">
<meta name="twitter:card" content="summary_large_image">
