# Laravel Broadcasting Setup (Step by Step)

This file explains exactly how broadcasting was implemented in your project, in simple language.

## 1) Enable Broadcasting in App Bootstrap

File: `bootstrap/app.php`

I added broadcasting registration with auth middleware:

```php
->withBroadcasting(
    __DIR__.'/../routes/channels.php',
    ['middleware' => ['web', 'auth']]
)
```

Why:
- This enables `/broadcasting/auth`.
- `web` + `auth` middleware prevent 403 for logged-in users and secure private channels.

---

## 2) Configure Driver and Pusher Keys

Files:
- `.env`
- `config/broadcasting.php`

### `.env` values used

```env
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=sync

PUSHER_APP_ID=2137918
PUSHER_APP_KEY=6c12c99d8c538f66e9e8
PUSHER_APP_SECRET=c0670c6ecbec12282a61
PUSHER_APP_CLUSTER=ap2
PUSHER_PORT=443
PUSHER_SCHEME=https
```

### `config/broadcasting.php`

Default connection set to:

```php
'default' => env('BROADCAST_CONNECTION', 'pusher'),
```

Why:
- Laravel sends events to Pusher.
- `sync` queue makes events fire immediately (easy for local testing).

---

## 3) Secure Private Channels

File: `routes/channels.php`

```php
Broadcast::channel('admin.orders', function ($user) {
    return $user && $user->role === 'admin';
});

Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

Why:
- Only admin can listen to `admin.orders`.
- A normal user can listen only to their own `orders.{id}` channel.

---

## 4) Create Broadcast Events

### A) Admin notification event (new order)

File: `app/Events/OrderPlaced.php`

What was done:
- Implements `ShouldBroadcast`.
- Uses `PrivateChannel('admin.orders')`.
- Event alias set by `broadcastAs()` to `order.placed`.
- Sends clean payload via `broadcastWith()`.

### B) User notification event (status changed)

File: `app/Events/OrderStatusUpdated.php`

What was done:
- Implements `ShouldBroadcast`.
- Uses `PrivateChannel('orders.' . $this->order->user_id)`.
- Event alias set by `broadcastAs()` to `order.status.updated`.
- Sends order id, number, status, updated_at.

---

## 5) Dispatch Events from Services

### A) Order placed flow

File: `app/Services/CheckoutService.php`

After order is created:

```php
Log::info('Broadcasting new order placed event', [...]);
event(new OrderPlaced($order));
```

### B) Order status update flow

File: `app/Services/Admin/OrderService.php`

After status update:

```php
Log::info('Broadcasting order status update event', [...]);
event(new OrderStatusUpdated($order));
```

Why:
- Events are fired from business logic layer.
- Logs help confirm event trigger during debugging.

---

## 6) Setup Echo + Pusher on Frontend

File: `resources/js/bootstrap.js`

What was done:
- Configure `axios` + CSRF token.
- Initialize `window.Echo` with pusher settings.
- Set `authEndpoint: '/broadcasting/auth'`.

Why:
- Echo needs CSRF/auth headers for private channel auth.

---

## 7) Listen for Events in UI

### A) Admin listener

File: `resources/js/admin.js`

Listens on:

```js
Echo.private('admin.orders').listen('.order.placed', ...)
```

Shows toast-style notification in admin UI.

### B) User listener

File: `resources/js/orders.js`

Listens on:

```js
Echo.private(`orders.${userId}`).listen('.order.status.updated', ...)
```

Shows notification when status changes.

Important:
- Dot prefix (`.order.placed`) is required because event uses `broadcastAs()`.

---

## 8) Pass Logged-In User Data to JS

Files:
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/app.blade.php`

Added meta tags:

```blade
<meta name="auth-user-id" content="{{ auth()->id() }}">
<meta name="auth-user-role" content="{{ auth()->user()->role }}">
```

Why:
- JS uses these values to subscribe to correct private channel.

---

## 9) Build and Run

Use these commands after changes:

```bash
php artisan optimize:clear
npm install
npm run dev
php artisan serve
```

If you use queue driver other than `sync`, run:

```bash
php artisan queue:work
```

---

## 10) Quick Test Flow

1. Login as admin in one browser.
2. Login as user in another browser.
3. Place new order as user.
4. Admin should get `New Order` notification.
5. Update order status as admin.
6. Same user should get status update notification.

---

## 11) Debug Checklist (If Notification Not Showing)

1. Check `.env` keys/cluster are correct.
2. Run `php artisan optimize:clear`.
3. Confirm `/broadcasting/auth` exists and user is logged in.
4. Confirm frontend bundles loaded (`npm run dev`).
5. Check logs in `storage/logs/laravel.log` for `Broadcasting ... event`.
6. Check browser console for Pusher auth/subscription errors.

---

## Final Summary

Broadcasting now works with:
- private channel authorization,
- secure per-role/per-user channel access,
- proper `ShouldBroadcast` events,
- Echo listeners with correct channel/event naming,
- and logging-based debugging support.
