<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy – Wallaby</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Wallaby" />
    <link rel="manifest" href="/site.webmanifest" />

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-lilac-50 text-lilac-950">
    <x-page-nav />

    <!-- Content -->
    <main class="max-w-5xl mx-auto px-6 py-8 lg:py-12">
        <h1 class="text-3xl font-display font-semibold mb-8">Privacy Policy</h1>
        
        <div class="prose prose-gray max-w-none space-y-4">
            <p class="text-lg text-gray-600">
                Last updated: {{ date('F j, Y') }}
            </p>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">What we collect</h2>
                <p class="text-gray-600 mb-4">Wallaby collects only the data necessary to provide our expense tracking service:</p>
                <ul class="text-gray-600 space-y-2">
                    <li><strong>Account information:</strong> Name, email address, and encrypted password</li>
                    <li><strong>Expense data:</strong> Descriptions, amounts, dates, and categorization (recurring/personal)</li>
                    <li><strong>Space data:</strong> Connection between you and your partner</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">What we don't collect</h2>
                <ul class="text-gray-600 space-y-2">
                    <li>Payment or banking information</li>
                    <li>Location data</li>
                    <li>Tracking cookies or advertising identifiers</li>
                    <li>Data from third-party services</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">How we use your data</h2>
                <ul class="text-gray-600 space-y-2">
                    <li>To provide the expense tracking and splitting functionality</li>
                    <li>To send transactional emails (verification, settlement notifications)</li>
                    <li>To share expense data with your connected partner</li>
                </ul>
                <p class="text-gray-600 mt-4">We do not sell, rent, or share your data with third parties for marketing purposes.</p>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">Data storage & security</h2>
                <ul class="text-gray-600 space-y-2">
                    <li>Your data is stored on servers located in Switzerland/EU</li>
                    <li>Passwords are hashed using industry-standard algorithms</li>
                    <li>All connections are encrypted via HTTPS</li>
                    <li>We perform regular backups to prevent data loss</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">Data sharing with your partner</h2>
                <p class="text-gray-600">
                    When you connect with a partner via invite code, they can see:
                </p>
                <ul class="text-gray-600 space-y-2 mt-4">
                    <li>Your name</li>
                    <li>Expenses you've logged (description, amount, date)</li>
                    <li>Monthly balances and settlements</li>
                </ul>
                <p class="text-gray-600 mt-4">Your email address and password are never shared with your partner.</p>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">Your rights</h2>
                <p class="text-gray-600 mb-4">You have the right to:</p>
                <ul class="text-gray-600 space-y-2">
                    <li><strong>Access:</strong> View all data we store about you</li>
                    <li><strong>Correct:</strong> Update your personal information</li>
                    <li><strong>Delete:</strong> Request deletion of your account and all associated data</li>
                    <li><strong>Export:</strong> Request a copy of your data</li>
                </ul>
                <p class="text-gray-600 mt-4">You can delete your account at any time from your profile settings. If you share a space with someone, the expenses you added stay visible to them, without your name or email, so their records still add up. Once nobody uses the space any more, everything in it is deleted.</p>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">Cookies</h2>
                <p class="text-gray-600">
                    We use only essential cookies required for the application to function (session management, CSRF protection). 
                    We do not use tracking or advertising cookies.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-display font-semibold mt-4 mb-2">Contact</h2>
                <p class="text-gray-600">
                    For privacy-related questions or requests, contact us at:
                    <a href="mailto:privacy@kimiyu.ch" class="text-lilac-950 underline decoration-1 underline-offset-4 hover:no-underline">privacy@kimiyu.ch</a>
                </p>
            </section>
        </div>
    </main>

    <x-page-footer />
</body>
</html>
