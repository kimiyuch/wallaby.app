# kimiyu

**Split expenses, not relationships.**

A simple expense tracking app for couples. Track shared costs, see who owes what, and settle up at the end of each month.

## Features

- 📊 **Track expenses** — Add one-time or recurring expenses
- 👫 **Partner sharing** — Connect with your partner via invite code
- ⚖️ **Auto-split** — 50/50 split with balance calculation
- 💝 **Personal expenses** — Mark expenses paid on behalf of partner (100% theirs)
- 📅 **Monthly view** — Navigate between months, settle when done
- ✅ **Settlements** — Lock months and get notified when settled

## Tech Stack

- **Laravel 12** — PHP framework
- **Livewire 3** — Reactive components
- **Tailwind CSS** — Styling
- **Alpine.js** — JavaScript interactions
- **SQLite** — Database
- **Pest** — Testing (74 tests, 178 assertions)

## Installation

```bash
# Clone
git clone https://github.com/marceli-to/kimiyu.ch.git
cd kimiyu.ch

# Install dependencies
composer install
npm install

# Setup
cp .env.example .env
php artisan key:generate
php artisan migrate

# Build assets
npm run build

# Run
php artisan serve
```

## Development

```bash
# Watch assets
npm run dev

# Run tests
./vendor/bin/pest

# Run tests in parallel
./vendor/bin/pest --parallel

# Code style
./vendor/bin/pint
```

## Tests

**74 tests, 178 assertions**

| Suite | Coverage |
|-------|----------|
| BalanceCalculator | Equal splits, unequal splits, personal expenses, decimals |
| Expense Model | Relationships, casts, recurring logic |
| Space Model | Invite codes, user limits, ownership |
| ExpenseForm | Create, edit, validation, settled months |
| Expenses Page | Auth, CRUD, navigation, settlements, balance |
| Auth | Login, logout, registration, password reset, email verification |

## How It Works

1. **Sign up** and create your space
2. **Share invite code** with your partner
3. **Add expenses** as you spend
4. **Check balance** to see who owes what
5. **Settle the month** when you're done

### Balance Calculation

- **Shared expenses** are split 50/50
- **Personal expenses** (paid for partner) are 100% owed by partner
- Positive balance = you owe · Negative balance = you get back

## License

MIT
