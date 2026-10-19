# Nexora Parent Hub

The central hub for the NexoraEDU parent app and web portal.

- Every NexoraEDU institute registers itself and its students here, using the shared `PARENT_HUB_SECRET`.
- Parents and students log in **once** with the student ID; the hub finds their institute and checks the password with that institute's system.
- Hub admins manage institutes, students, app versions and the portal notice from the same login page.

## One login page (`/login`)

| Typed in the first box | Who            | Goes to             |
|------------------------|----------------|---------------------|
| An email (`a@b.lk`)    | Hub admin      | `/admin`            |
| A student ID (`SA10001`) | Parent/student | `/portal`         |

Wrong passwords are rate limited (`HUB_LOGIN_ATTEMPTS`, default 5 per minute).

### Parent portal (`/portal`)
Home, attendance, fees (month by month), exams and results, class schedule, messages, profile and password change.
Data is read live from the student's institute; the hub stores no marks or payments.

### Notifications in the browser
When the `FIREBASE_*` values are set, the portal asks parents to turn on notifications. The browser's FCM token is
sent to the student's institute (`POST {institute}/api/parent/v1/fcm/token`, `device_type=web`) and saved in its
`fcm_tokens` table, so the institute's push notifications reach the browser like the app. Logging out marks the
token inactive. Institutes that do not accept `web` yet get the token as `android` (delivery is the same).

### Admin area (`/admin`)
Dashboard, institutes (edit URL, enable/disable, last seen), students (search, block/unblock), parent app versions
(latest / force update), admins (add, disable), settings (support phone/email, portal notice).

## API (used by the institutes and the mobile app)

| Method | Path                            | Auth                 |
|--------|---------------------------------|----------------------|
| POST   | `/api/v1/register-institute`    | `secret_key`         |
| POST   | `/api/v1/register-student`      | `secret_key`         |
| POST   | `/api/v1/login`                 | username + password  |
| GET    | `/api/v1/app-version?platform=android&current=1.3.0` | none |

## Setup

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env            # set DB_*, APP_URL, PARENT_HUB_SECRET
php artisan key:generate
php artisan migrate --force
php artisan hub:admin admin@nexorait.lk --name="Isuru"   # asks for a password
npm ci && npm run build
php artisan optimize
```

`php artisan hub:admin <email>` also resets the password of an existing admin.

## Tests

```bash
php artisan test
```
