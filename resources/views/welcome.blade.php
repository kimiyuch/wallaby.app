<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>kimiyu – Split expenses, not relationships.</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="kimiyu" />
    <link rel="manifest" href="/site.webmanifest" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-100 text-gray-900 overflow-x-hidden">
    <!-- Animated Background Orbs -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>

    <x-page-nav />

    <!-- Hero -->
    <section class="min-h-screen flex items-center justify-center">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white rounded-full text-sm text-gray-600 mb-8 shadow-sm">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Simple expense tracking for two
            </div>
            
            <h1 class="text-5xl sm:text-6xl font-bold tracking-tight mb-6">
                Split expenses,<br>
                <span class="text-gray-400">not relationships.</span>
            </h1>
            
            <p class="text-lg text-gray-500 mb-10 max-w-xl mx-auto">
                kimiyu helps couples and housemates track and split expenses fairly. No spreadsheets, no awkward math — just clarity.
            </p>
            
            <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition-colors">
                See how it works →
            </a>
        </div>
    </section>

    <!-- Features -->
    <section id="how-it-works" class="py-24">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">How it works</h2>
                <p class="text-gray-500">Three simple steps to fair expense sharing</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Log expenses</h3>
                    <p class="text-gray-500 text-sm">Add expenses as they happen — school fees, clothes, activities. It takes seconds.</p>
                </div>
                
                <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">See the balance</h3>
                    <p class="text-gray-500 text-sm">Instantly see who paid what and who owes whom. Fair 50/50 split, automatically calculated.</p>
                </div>
                
                <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Stay in sync</h3>
                    <p class="text-gray-500 text-sm">Both parents see the same data. Transparency builds trust and reduces conflict.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="py-24 bg-white/60 backdrop-blur-sm">
        <div class="max-w-5xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-8">Built for real life</h2>
                    <div class="space-y-4">
                        <div class="bg-white/60 rounded-xl p-5 border border-gray-200/40">
                            <h4 class="font-medium mb-1">Recurring expenses</h4>
                            <p class="text-sm text-gray-500">Set up monthly costs once — subscriptions, lessons, allowances — and they appear automatically.</p>
                        </div>
                        <div class="bg-white/60 rounded-xl p-5 border border-gray-200/40">
                            <h4 class="font-medium mb-1">Month-by-month view</h4>
                            <p class="text-sm text-gray-500">Navigate through history. See what was spent when, and track balances over time.</p>
                        </div>
                        <div class="bg-white/60 rounded-xl p-5 border border-gray-200/40">
                            <h4 class="font-medium mb-1">No learning curve</h4>
                            <p class="text-sm text-gray-500">Clean, simple interface. Add an expense in under 10 seconds.</p>
                        </div>
                    </div>
                </div>
                <!-- Mock App UI -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden" data-mock-app>
                    <!-- App Header -->
                    <div class="bg-gray-100 text-gray-900 px-5 py-3 flex items-center justify-between border-b border-gray-200/60">
                        <div class="font-medium text-sm">January 2026</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            <span>Synced</span>
                        </div>
                    </div>
                    
                    <!-- Balance Card -->
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Balance</div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-green-600 tabular-nums">+<span class="counter" data-target="42.50">0</span></span>
                            <span class="text-sm text-gray-400">owed to you</span>
                        </div>
                    </div>
                    
                    <!-- Expense List -->
                    <div class="divide-y divide-gray-100">
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-sm">School supplies</div>
                                <div class="text-xs text-gray-400">Peter paid</div>
                            </div>
                            <div class="text-sm font-semibold tabular-nums"><span class="counter" data-target="45.00">0</span></div>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-sm">New sneakers</div>
                                <div class="text-xs text-gray-400">Maria paid</div>
                            </div>
                            <div class="text-sm font-semibold tabular-nums"><span class="counter" data-target="89.00">0</span></div>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-sm">Mobile Subscription</div>
                                <div class="text-xs text-gray-400">Peter · Monthly</div>
                            </div>
                            <div class="text-sm font-semibold tabular-nums"><span class="counter" data-target="29.00">0</span></div>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-sm">Netflix</div>
                                <div class="text-xs text-gray-400">Maria · Monthly</div>
                            </div>
                            <div class="text-sm font-semibold tabular-nums"><span class="counter" data-target="15.90">0</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing (hidden for beta)
    <section id="pricing" class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Simple pricing</h2>
                <p class="text-gray-500">One plan. Everything included. No hidden fees.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <!-- Monthly -->
                <div class="bg-white rounded-2xl p-8 border-2 border-gray-200">
                    <div class="text-sm text-gray-500 mb-2">Monthly</div>
                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-4xl font-bold">$5</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Unlimited expenses
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Both parents included
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Recurring expenses
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Full history
                        </li>
                    </ul>
                    <x-secondary-button as="a" href="{{ route('register') }}" class="w-full justify-center" size="lg">
                        Get started
                    </x-secondary-button>
                </div>

                <!-- Annual -->
                <div class="bg-gray-900 rounded-2xl p-8 text-white relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        Save $10
                    </div>
                    <div class="text-sm text-gray-400 mb-2">Annual</div>
                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-4xl font-bold">$50</span>
                        <span class="text-gray-400">/year</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Everything in monthly
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            2 months free
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Priority support
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Lock in price forever
                        </li>
                    </ul>
                    <x-primary-button as="a" href="{{ route('register') }}" variant="light" class="w-full" size="lg">
                        Get started
                    </x-primary-button>
                </div>
            </div>

            <p class="text-center text-sm text-gray-500 mt-8">
                14-day free trial. No credit card required.
            </p>
        </div>
    </section>
    --}}

    <!-- CTA -->
    <section class="py-24 bg-white/40">
        <div class="max-w-5xl mx-auto px-6">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-4xl font-bold mb-4">Ready to simplify?</h2>
                <p class="text-gray-500 mb-8">Join parents who've made expense sharing stress-free.</p>
                <x-primary-button as="a" href="{{ route('register') }}" size="lg">
                    Get started
                </x-primary-button>
            </div>
        </div>
    </section>

    <x-page-footer />
</body>
</html>
