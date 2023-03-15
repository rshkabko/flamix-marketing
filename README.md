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

$flamixMarketing->email
    ->order('order_confirm', 9, ['items' => 999, 'delivery' => 100], 'UAH', 'Львів, Миколайчука 36, Поштомат Нової пошти', 'http://localhost/admin/companies')
    ->setItem([
            [
                'url' => 'https://4mobi.com.ua/product/picture-shadow-matte-case-new-pc-tpu-iphone-11-pro-in-outer-space-nasa-light-purple/',
                'img' => 'https://4mobi.com.ua/upload/resize_cache/webp/iblock/f16/emm52lkm7g6duc004kjmbvbbomxp4i60/450_450_0/picture_shadow_matte_case_new_pc_tpu_iphone_11_pro_in_outer_space_nasa_light_purple.webp',
                'name' => 'Чехол с дизайном Picture Shadow Matte Case New (PC+TPU) iPhone 11 Pro in outer space nasa/light purple',
                'count' => 1,
                'price' => 999,
            ],
            // Many goods...
        ])
    ->send('flamix.solutions', 'client@gmail.com');
```

### SMS

```php
    $sms = new \Flamix\Marketing\Actions\SMS('token', '4mobi.com.ua');
    $result = $sms->send('380988220142', 'hello!');
```