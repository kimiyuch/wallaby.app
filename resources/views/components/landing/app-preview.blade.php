{{-- A static copy of the expenses screen, seen by Mia. The numbers match the worked example in the balance section. --}}
@php
    $mia = [
        ['description' => 'Groceries', 'amount' => '86.40'],
        ['description' => 'Electricity', 'amount' => '64.00', 'badge' => 'Recurring'],
        ['description' => 'Cinema tickets', 'amount' => '32.00'],
    ];

    $jonas = [
        ['description' => 'Internet', 'amount' => '49.90', 'badge' => 'Recurring'],
        ['description' => 'Takeaway pizza', 'amount' => '38.50'],
        ['description' => 'Plants', 'amount' => '24.00'],
        ['description' => 'Concert ticket', 'amount' => '59.00', 'badge' => 'Paid for partner'],
    ];

    $stats = [
        ['label' => 'Mia', 'value' => '182.40', 'note' => 'This month'],
        ['label' => 'Jonas', 'value' => '171.40', 'note' => 'This month'],
        ['label' => 'Total', 'value' => '353.80', 'note' => 'Combined expenses'],
    ];

    $badges = [
        'Recurring' => 'bg-lilac-100 text-lilac-700',
        'Paid for partner' => 'bg-gold-100 text-gold-800',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-lilac-50 p-2 shadow-xl shadow-lilac-950/10 ring-1 ring-lilac-950/10']) }}
     role="img" aria-label="The Wallaby expenses screen for October: Mia paid 182.40, Jonas paid 171.40, and Mia owes Jonas 24.00.">
    <div class="flex" aria-hidden="true">
        {{-- Sidebar --}}
        <div class="flex w-48 shrink-0 flex-col gap-y-6 px-4 py-3 max-lg:hidden">
            <div class="flex items-center gap-x-2 font-display text-lg font-semibold text-lilac-950">
                Wallaby
            </div>
            <div class="flex flex-col gap-y-5 text-sm">
                <div class="flex items-center gap-x-3 font-medium text-lilac-700">
                    <x-heroicon-o-receipt-percent class="size-5 shrink-0" />
                    Expenses
                </div>
                <div class="flex items-center gap-x-3 text-gray-500">
                    <x-heroicon-o-user class="size-5 shrink-0" />
                    Profile
                </div>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="min-w-0 flex-1 rounded-lg bg-white p-4 text-sm sm:p-6">
            <div class="flex items-center justify-between gap-x-4">
                <div class="flex items-center gap-x-3">
                    <x-heroicon-m-plus class="size-5 shrink-0 fill-gray-400" />
                    <div class="font-display text-lg font-semibold text-lilac-950">Expenses</div>
                </div>
                <div class="flex items-center gap-x-3 text-gray-500">
                    <x-heroicon-m-chevron-left class="size-5 shrink-0 fill-gray-400" />
                    <span class="tabular-nums">October 2026</span>
                    <x-heroicon-m-chevron-right class="size-5 shrink-0 fill-gray-400" />
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="rounded-lg p-3 ring-1 ring-gray-950/10 sm:p-4">
                        <div class="text-gray-500">{{ $stat['label'] }}</div>
                        <div class="mt-0.5 text-lg font-semibold text-lilac-950 tabular-nums sm:text-xl">{{ $stat['value'] }}</div>
                        <div class="text-xs text-gray-400">{{ $stat['note'] }}</div>
                    </div>
                @endforeach
                <div class="rounded-lg p-3 ring-1 ring-gray-950/10 sm:p-4">
                    <div class="text-gray-500">Balance</div>
                    <div class="mt-0.5 text-lg font-semibold text-red-600 tabular-nums sm:text-xl">-24.00</div>
                    <div class="text-xs text-gray-400">You owe Jonas</div>
                </div>
            </div>

            <div class="mt-8 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                @foreach (['Mia' => $mia, 'Jonas' => $jonas] as $name => $expenses)
                    <div>
                        <div class="border-b border-gray-950/10 pb-2 font-semibold text-lilac-950">{{ $name }}</div>
                        <div class="divide-y divide-gray-950/5">
                            @foreach ($expenses as $expense)
                                <div class="flex items-center justify-between gap-x-3 py-2.5">
                                    <div class="flex min-w-0 items-center gap-x-2">
                                        <span class="truncate text-lilac-950">{{ $expense['description'] }}</span>
                                        @isset($expense['badge'])
                                            <span class="shrink-0 rounded-sm px-2 py-0.5 text-xs {{ $badges[$expense['badge']] }} max-sm:hidden">{{ $expense['badge'] }}</span>
                                        @endisset
                                    </div>
                                    <span class="text-lilac-950 tabular-nums">{{ $expense['amount'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
