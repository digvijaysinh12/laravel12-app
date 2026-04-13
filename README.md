# Laravel Mini Admin Panel

This is a beginner-friendly Laravel 12 project for learning:

- Authentication (login/register)
- Role-based access (admin/user)
- Product, cart, checkout, invoice flow
- Admin dashboard
- Real-time notifications using Laravel Broadcasting + Pusher

The project is written in a simple way so a student can understand each part.

---

## 1. What This Project Does

### User side
- User can view products
- User can add products to cart
- User can checkout and create an order
- User can see invoice and download PDF

### Admin side
- Admin can open dashboard
- Admin can manage products
- Admin can manage orders
- Admin gets real-time notification when a new order is placed

---

## 2. Tech Stack

- PHP 8+
- Laravel 12
- MySQL
- Blade
- Tailwind CSS
- Vite
- Laravel Echo
- Pusher

---

## 3. Important Folders

```txt
app/
  Events/                 # Broadcasting events
  Http/Controllers/       # Controllers
  Services/               # Business logic

routes/
  web.php
  admin.php
  user.php
  channels.php            # Broadcasting channel auth

resources/
  views/                  # Blade UI
  js/                     # Echo and frontend JS

config/
  broadcasting.php
  queue.php
```

---

## 4. Installation (Step by Step)

### 4.1 Clone and install

```bash
git clone <your-repo-url>
cd laravel12-app
composer install
npm install
```

### 4.2 Environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 4.3 Set database in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 4.4 Run migration

```bash
php artisan migrate
```

### 4.5 Run app

Use two terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

---

## 5. Broadcasting Setup (Important)

This project uses **Pusher private channels**.

### 5.1 `.env` broadcasting values

```env
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=sync

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=ap2

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
```

After editing `.env`, run:

```bash
php artisan config:clear
php artisan view:clear
```

---

## 6. Broadcasting Code Flow (Simple)

### 6.1 Event example

`app/Events/OrderStatusUpdated.php`

```php
class OrderStatusUpdated implements ShouldBroadcast, ShouldRescue
{
    public function broadcastOn(): array
    {
        return [new PrivateChannel('orders.'.$this->order->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }
}
```

### 6.2 Channel authorization

`routes/channels.php`

```php
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('admin.orders', function ($user) {
    return $user && $user->role === 'admin';
});
```

### 6.3 Echo frontend listener

`resources/js/admin.js`

```js
window.Echo.private('admin.orders')
    .listen('.order.placed', (event) => {
        // show notification
    });
```

---

## 7. How to Test Realtime Notification

1. Login as **admin** and open dashboard.
2. Login as **user** in another browser and place order.
3. Admin dashboard should show new order notification.

If it does not show:

- Check `.env` Pusher key and cluster
- Check browser console errors
- Check Laravel log: `storage/logs/laravel.log`
- Run:

```bash
php artisan route:list --path=broadcasting
```

You should see:

`/broadcasting/auth`

---

## 8. Useful Commands

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

```bash
php artisan route:list
```

```bash
php artisan view:cache
```

---

## 9. Notes for Beginner Students

- Start by reading route files (`routes/web.php`, `routes/admin.php`, `routes/user.php`).
- Then read controllers and service classes.
- Then check Blade files in `resources/views`.
- For real-time, understand this order:

`Controller/Service -> Event -> Channel -> Echo Listener -> UI Notification`

---

## 10. Common Problems

### Problem: Notification not showing
- Wrong Pusher key/cluster
- Old config cache
- Not logged in as admin
- JS not built (`npm run dev`)

### Problem: `/broadcasting/auth` gives 403
- User is not authenticated
- Missing CSRF token in page
- Channel authorization returns false

---

## 11. Author

Digvijaysinh Sarvaiya


## Artisan Commands Used

### php artisan list
Shows all available Artisan commands in Laravel.

### php artisan route:list
Displays all routes (admin + customer).
Used to verify routing structure.

### php artisan db:show
Shows database connection details and tables.

### php artisan cache:clear
Clears application cache.

### cache:clear vs config:clear
- cache:clear → clears runtime cache (data)
- config:clear → clears cached config values