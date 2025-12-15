# app:match-open-ordersTrading Platform (Laravel + Vue)

A simple platform built with **Laravel API** and **Vue 3 (Composition API)**.  
It supports user authentication, asset balances, order placement, order matching, and real-time updates via **Pusher**.

---

## 🛠 Tech Stack

- **Backend:** Laravel 12 (API)
- **Frontend:** Vue 3 + Vite (Composition API)
- **Auth:** Laravel Sanctum (SPA / cookie-based)
- **Database:** MySQL / PostgreSQL
- **Real-time:** Pusher + Laravel Echo
- **Styling:** Tailwind CSS

---

## ⚙️ Setup

### 1. Clone & Install
```bash
git clone <repo-url>
cd project
composer install
npm install
```


### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```

## Update .env vars
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_pass

BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=xxx
PUSHER_APP_KEY=xxx
PUSHER_APP_SECRET=xxx
PUSHER_APP_CLUSTER=mt1


### 3. Database
```bash
php artisan migrate --seed
```

### 4. Build and run services
```bash
npm run build

php artisan queue:work
php artisan schedule:work
```

## production (cron)
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1


### 4. Testing
- Migrate and seed database before testing

```bash
php artisan migrate --seed
php artisan test
```
