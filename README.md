# Flamix Marketing PHP SDK

Plain PHP client (Guzzle) for the Flamix Marketing API: email, SMS, short links and QR codes.

## Install

```bash
composer require flamix/marketing-php-sdk
```

```php
use Flamix\Marketing\Client as FlamixMarketing;

(new FlamixMarketing('token', 'en'))->email()
    ->setSubject('Hello')
    ->setBody('<p>HTML body</p>')
    ->send('flamix.solutions', 'client@example.com');
```

## Documentation

See [docs/](docs/) for full documentation.

AI: Laravel Boost guideline and skills in resources/boost/.

- [Email](docs/email.md) — client, templates, the shared `$email` property, payload
- [SMS](docs/sms.md) — `Actions\SMS`
- [Links](docs/links.md) — `lnk()`, `qr()`, `get()`
- [Errors](docs/errors.md) — status codes to exceptions, non-JSON answers
