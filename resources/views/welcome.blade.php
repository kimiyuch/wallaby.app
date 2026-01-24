<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kimiyu — Shared Expenses Made Simple</title>
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

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold">Kimiyu</a>
            <div class="flex items-center gap-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="min-h-screen flex items-center justify-center px-6 pt-20">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white rounded-full text-sm text-gray-600 mb-8 shadow-sm">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Simple expense tracking for co-parents
            </div>
            
            <h1 class="text-5xl sm:text-6xl font-bold tracking-tight mb-6">
                Split expenses,<br>
                <span class="text-gray-400">not relationships.</span>
            </h1>
            
            <p class="text-lg text-gray-500 mb-10 max-w-xl mx-auto">
                Kimiyu helps separated parents track and split their children's expenses fairly. No spreadsheets, no awkward conversations — just clarity.
            </p>
            
            <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition-colors">
                See how it works →
            </a>
        </div>
    </section>

    <!-- Features -->
    <section id="how-it-works" class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">How it works</h2>
                <p class="text-gray-500">Three simple steps to fair expense sharing</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/40 backdrop-blur-sm rounded-2xl p-8">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Log expenses</h3>
                    <p class="text-gray-500 text-sm">Add expenses as they happen — school fees, clothes, activities. It takes seconds.</p>
                </div>
                
                <div class="bg-white/40 backdrop-blur-sm rounded-2xl p-8">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">See the balance</h3>
                    <p class="text-gray-500 text-sm">Instantly see who paid what and who owes whom. Fair 50/50 split, automatically calculated.</p>
                </div>
                
                <div class="bg-white/40 backdrop-blur-sm rounded-2xl p-8">
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
    <section class="py-24 px-6 bg-white/40 backdrop-blur-sm">
        <div class="max-w-5xl mx-auto">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">Built for real life</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">Recurring expenses</h4>
                                <p class="text-sm text-gray-500">Set up monthly costs once — subscriptions, lessons, allowances — and they appear automatically.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">Month-by-month view</h4>
                                <p class="text-sm text-gray-500">Navigate through history. See what was spent when, and track balances over time.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium mb-1">No learning curve</h4>
                                <p class="text-sm text-gray-500">Clean, simple interface. Add an expense in under 10 seconds.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 rounded-2xl p-8 aspect-square flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-6xl font-bold text-gray-900 mb-2">50/50</div>
                        <div class="text-gray-500">Fair. Transparent. Simple.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
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
                    <a href="{{ route('register') }}" class="block w-full py-3 text-center border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        Get started
                    </a>
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
                    <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-white text-gray-900 rounded-xl hover:bg-gray-100 transition-colors font-medium">
                        Get started
                    </a>
                </div>
            </div>

            <p class="text-center text-sm text-gray-500 mt-8">
                14-day free trial. No credit card required.
            </p>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 px-6 bg-white/40 backdrop-blur-sm">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to simplify?</h2>
            <p class="text-gray-500 mb-8">Join parents who've made expense sharing stress-free.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-colors font-medium">
                Start your free trial
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-6 border-t border-gray-200">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-500">
                    Copyright {{ date('Y') }} – Kimiyu. All rights reserved.
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="hover:text-gray-900 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-900 transition-colors">Register</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Credit -->
    <div class="pt-4 pb-4 px-6">
        <div class="text-center text-[0.625rem] text-gray-400 font-mono">
            Made with ❤️ by <a href="https://marceli.to" target="_blank" class="hover:text-gray-600 transition-colors">marceli.to</a>
        </div>
    </div>
</body>
</html>
