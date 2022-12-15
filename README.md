## Install

```php
composer require flamix/marketing-php-sdk
```

## Email

We can send custom and standard mails.

```php
use Flamix\Marketing\Client as FlamixMarketing;

// Init
$flamixMarketing = new FlamixMarketing('token', 'en');

// Custom mail
$flamixMarketing->email
    ->setSubject('Super Creative Subject')
    ->setBody('<p>Put HTML text here!</p>')
    ->send('flamix.solutions', 'client@gmail.com', ['title' => 'Optionally you can set title']);

// License mail
$flamixMarketing->email
    ->license('FX-1234567890', 'WooCommerce Store Integrations')
    ->send('flamix.solutions', 'client@gmail.com');

// Payment mail
$flamixMarketing->email
    ->payment('debit', 120, 'USD', ['id' => 999, 'date' => '10/12/2022', 'method' => 'Flamix.Kassa'])
    ->setUser(['id' => 1, 'name' => 'Roman Shkabko', 'region' => 'UA', 'currency' => 'UAH'])
    ->setItem(['id' => 123123, 'name' => 'WooCommerce Store Sync'], 'FX-123456789')
    ->send('flamix.solutions', 'client@gmail.com');
```
