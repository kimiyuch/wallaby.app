@php
    $steps = [
        ['title' => 'Create your space', 'body' => 'Sign up and Wallaby gives you a three-word invite code to share.'],
        ['title' => 'Invite your person', 'body' => 'Your partner or housemate signs up with the code, and you’re connected. A space is for two people.'],
        ['title' => 'Add expenses as they happen', 'body' => 'Groceries, rent, a dinner out. Wallaby splits each one and keeps the balance up to date.'],
    ];

    $rules = [
        ['icon' => 'scale', 'title' => 'Shared costs split 50/50', 'body' => 'Everything you both use is split down the middle, no matter who paid.'],
        ['icon' => 'gift', 'title' => 'Paid for the other person', 'body' => 'Bought something that’s only for them? Mark it, and the full amount goes on their side.'],
        ['icon' => 'banknotes', 'title' => 'One number at the end', 'body' => 'Wallaby nets it all out, so there’s a single amount for one of you to send.'],
    ];

    $features = [
        ['icon' => 'arrow-path', 'title' => 'Recurring expenses', 'body' => 'Add rent, utilities or subscriptions once. They show up every month until you give them an end date.'],
        ['icon' => 'calendar-days', 'title' => 'Month by month', 'body' => 'Flip back through past months to see what was spent, by whom, and how the balance came out.'],
        ['icon' => 'check-badge', 'title' => 'Settle up', 'body' => 'Once you’ve squared up, mark the month as settled. It’s locked, and you both get an email.'],
        ['icon' => 'device-phone-mobile', 'title' => 'On your phone', 'body' => 'Wallaby runs in the browser. Add it to your home screen and it opens like an app.'],
        ['icon' => 'user-group', 'title' => 'Couples and housemates', 'body' => 'Share a flat, a household or a life. Wallaby works the same for any two people who split costs.'],
        ['icon' => 'lock-closed', 'title' => 'Private by design', 'body' => 'Only the two of you see your expenses. No ads, no tracking cookies, and your data stays in Switzerland or the EU.'],
    ];

    $faqs = [
        ['question' => 'Can more than two people share a space?', 'answer' => 'No. Wallaby is made for two, so the balance is always one clear number between you and the other person.'],
        ['question' => 'Does Wallaby move any money?', 'answer' => 'No. Wallaby keeps the tab and tells you who owes whom. You pay each other however you like, then mark the month as settled.'],
        ['question' => 'What happens when we settle a month?', 'answer' => 'The month is locked so nothing changes by accident, and you both get an email confirming it. Past months stay there to look back on.'],
        ['question' => 'What can the other person see?', 'answer' => 'Your name, the expenses you log and the monthly balances. Your email address and password are never shared.'],
        ['question' => 'Can I delete my account?', 'answer' => 'Yes, at any time from your profile. Your account and its data are deleted with it.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wallaby – Split expenses, not relationships.</title>
    <meta name="description" content="Wallaby keeps a running tab for couples and housemates. Add what you paid, see who owes whom, and settle up once a month.">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Wallaby" />
    <link rel="manifest" href="/site.webmanifest" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-lilac-950">
    <div class="isolate">
        <div class="absolute inset-x-0 top-0 -z-10 h-[44rem] bg-gradient-to-b from-lilac-50 to-white"></div>

        <x-page-nav />

        <main>
            {{-- Hero --}}
            <section class="pt-10 pb-24 sm:pt-16 sm:pb-32">
                <div class="mx-auto max-w-6xl px-6 text-center lg:px-8">
                    <img src="/mascot.svg" width="724" height="800" fetchpriority="high" alt="" class="mx-auto h-auto w-32 sm:w-40">
                    <h1 class="mx-auto mt-8 max-w-[20ch] font-display text-5xl font-semibold tracking-tight text-balance sm:text-7xl">
                        Split expenses, <span class="text-lilac-600">not relationships</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-[48ch] text-lg/8 text-pretty text-gray-600 sm:text-xl/8">
                        Wallaby keeps a running tab for couples and housemates. Add what you paid, see who owes whom, and settle up once a month, without spreadsheets or awkward math.
                    </p>
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-4">
                        <a href="{{ route('register') }}" class="rounded-full bg-lilac-600 px-5 py-3 text-base/6 font-semibold text-white shadow-sm hover:bg-lilac-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lilac-600">
                            Create your space
                        </a>
                        <a href="#how-it-works" class="inline-flex items-center gap-x-1.5 text-base/6 font-semibold text-lilac-800 hover:text-lilac-600">
                            See how it works
                            <x-heroicon-m-arrow-right class="size-5 shrink-0 fill-current" />
                        </a>
                    </div>
                    <p class="mt-6 text-base/7 text-gray-500 sm:text-sm/6">Made for two people · Works on your phone and computer</p>

                    <x-landing.app-preview class="mx-auto mt-16 max-w-5xl text-left sm:mt-20" />
                </div>
            </section>

            {{-- How it works --}}
            <section id="how-it-works" class="scroll-mt-8 pb-24 sm:pb-32">
                <div class="mx-auto max-w-6xl px-6 lg:px-8">
                    <p class="text-base/7 font-semibold text-lilac-600">How it works</p>
                    <h2 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                        Set up in a minute, fair from then on
                    </h2>

                    <ol role="list" class="mt-16 grid grid-cols-1 gap-12 sm:grid-cols-3">
                        @foreach ($steps as $step)
                            <li class="border-t border-lilac-950/10 pt-6">
                                <p class="font-display text-base/7 font-semibold text-lilac-600 tabular-nums">0{{ $loop->iteration }}</p>
                                <h3 class="mt-2 text-lg/7 font-semibold">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-base/7 text-pretty text-gray-600">{{ $step['body'] }}</p>
                                @if ($loop->first)
                                    <p class="mt-4 inline-flex rounded-full bg-lilac-50 px-3 py-1 font-mono text-sm/6 text-lilac-800 ring-1 ring-lilac-950/10">blue-moon-snow</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>

            {{-- Balance --}}
            <section id="balance" class="scroll-mt-8 bg-lilac-950 py-24 sm:py-32">
                <div class="mx-auto grid max-w-6xl grid-cols-1 items-center gap-12 px-6 lg:grid-cols-2 lg:px-8">
                    <div>
                        <p class="text-base/7 font-semibold text-lilac-300">No awkward math</p>
                        <h2 class="mt-2 max-w-[24ch] font-display text-4xl font-semibold tracking-tight text-balance text-white sm:text-5xl">
                            Always know who owes whom
                        </h2>
                        <p class="mt-6 max-w-[48ch] text-lg/8 text-pretty text-lilac-100/70">
                            Wallaby does the sums the same way every month, so neither of you has to keep score.
                        </p>

                        <dl class="mt-12 flex flex-col gap-y-8">
                            @foreach ($rules as $rule)
                                <div>
                                    <dt class="flex items-start gap-x-3 text-base/7 font-semibold text-white">
                                        <x-dynamic-component :component="'heroicon-m-'.$rule['icon']" class="h-7 w-5 shrink-0 fill-lilac-300" />
                                        {{ $rule['title'] }}
                                    </dt>
                                    <dd class="mt-1 pl-8 text-base/7 text-pretty text-lilac-100/70">{{ $rule['body'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    {{-- Worked example: the same month as the screen above --}}
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 sm:p-8">
                        <p class="text-base/7 font-semibold text-white sm:text-sm/6">October, worked out</p>
                        <dl class="mt-6 divide-y divide-white/10 text-base/7 sm:text-sm/6">
                            <div class="flex justify-between gap-x-4 pb-4">
                                <dt class="text-lilac-100/70">Mia paid for both</dt>
                                <dd class="text-white tabular-nums">182.40</dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-4">
                                <dt class="text-lilac-100/70">Jonas paid for both</dt>
                                <dd class="text-white tabular-nums">112.40</dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-4">
                                <dt class="text-lilac-100/70">Each person’s half of 294.80</dt>
                                <dd class="text-white tabular-nums">147.40</dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-4">
                                <dt class="text-lilac-100/70">Mia paid 35.00 over her half, so Jonas owes her</dt>
                                <dd class="text-white tabular-nums">35.00</dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-4">
                                <dt class="text-lilac-100/70">Jonas bought Mia’s concert ticket, so she owes him</dt>
                                <dd class="text-white tabular-nums">59.00</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-x-4 pt-4">
                                <dt class="font-semibold text-white">Mia sends Jonas</dt>
                                <dd class="font-display text-3xl font-semibold text-mint-300 tabular-nums">24.00</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section id="features" class="scroll-mt-8 py-24 sm:py-32">
                <div class="mx-auto max-w-6xl px-6 lg:px-8">
                    <p class="text-base/7 font-semibold text-lilac-600">Features</p>
                    <h2 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                        Everything two people need to share costs
                    </h2>
                    <p class="mt-6 max-w-[48ch] text-lg/8 text-pretty text-gray-600">
                        Wallaby does one thing and keeps it simple. No budgets, no bank connections, no charts to study.
                    </p>

                    <dl class="mt-16 grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($features as $feature)
                            <div>
                                <dt class="flex items-start gap-x-3 text-base/7 font-semibold">
                                    <x-dynamic-component :component="'heroicon-m-'.$feature['icon']" class="h-7 w-5 shrink-0 fill-lilac-500" />
                                    {{ $feature['title'] }}
                                </dt>
                                <dd class="mt-1 pl-8 text-base/7 text-pretty text-gray-600">{{ $feature['body'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </section>

            {{-- FAQ --}}
            <section id="faq" class="scroll-mt-8 pb-24 sm:pb-32">
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 px-6 lg:grid-cols-3 lg:px-8">
                    <div>
                        <p class="text-base/7 font-semibold text-lilac-600">Questions</p>
                        <h2 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                            Good to know
                        </h2>
                    </div>
                    <dl class="divide-y divide-lilac-950/10 lg:col-span-2">
                        @foreach ($faqs as $faq)
                            <div class="py-6 first:pt-0 last:pb-0">
                                <dt class="text-base/7 font-semibold">{{ $faq['question'] }}</dt>
                                <dd class="mt-2 max-w-[56ch] text-base/7 text-pretty text-gray-600">{{ $faq['answer'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </section>

            {{-- Call to action --}}
            <section class="bg-lilac-50 py-24 sm:py-32">
                <div class="mx-auto max-w-6xl px-6 text-center lg:px-8">
                    <img src="/mascot.svg" width="724" height="800" alt="" class="mx-auto h-auto w-24">
                    <h2 class="mx-auto mt-8 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                        Start your shared tab
                    </h2>
                    <p class="mx-auto mt-6 max-w-[48ch] text-lg/8 text-pretty text-gray-600">
                        It takes a minute to set up. The other person joins with your invite code.
                    </p>
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-4">
                        <a href="{{ route('register') }}" class="rounded-full bg-white px-5 py-3 text-base/6 font-semibold text-lilac-800 shadow-sm ring-1 ring-lilac-950/10 hover:bg-lilac-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lilac-600">
                            Create your space
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-x-1.5 text-base/6 font-semibold text-lilac-800 hover:text-lilac-600">
                            Log in
                            <x-heroicon-m-arrow-right class="size-5 shrink-0 fill-current" />
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <x-page-footer />
    </div>
</body>
</html>
