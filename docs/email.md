# Email

Mail through the Flamix Marketing API (`https://pr.flamix.info/api/v1/`).

## Client

```php
use Flamix\Marketing\Client as FlamixMarketing;

$marketing = new FlamixMarketing('token', 'en'); // lang goes into the URL: mail/send/{company}/{lang}/{email}

$mail = $marketing->email(); // a fresh Email for every mail
```

`$marketing->email` (the property) is one shared instance: subject, body and data set on it stay for the next `send()`.

```php
$marketing->email->setSubject('A')->send('flamix.solutions', 'a@example.com');
$marketing->email->setBody('B')->send('flamix.solutions', 'b@example.com'); // still has subject "A"
```

## Plain HTML mail

```php
$marketing->email()
    ->setSubject('Super Creative Subject')
    ->setBody('<p>Put HTML text here!</p>')
    ->send('flamix.solutions', 'client@example.com', ['title' => 'Optional title']); // template "general"
```

## Template

```php
$marketing->email()
    ->custom('password_restore', ['link' => $url]) // = setTemplate() + setData()
    ->send('flamix.solutions', 'client@example.com');
```

## License

```php
$marketing->email()
    ->license('FX-1234567890', 'WooCommerce Store Integrations') // template "license"
    ->send('flamix.solutions', 'client@example.com');
```

## Payment

```php
$marketing->email()
    ->payment('debit', 120, 'USD', ['id' => 999, 'date' => '10/12/2022', 'method' => 'Flamix.Kassa']) // currency sent as "usd"
    ->setUser(['id' => 1, 'name' => 'Roman Shkabko', 'region' => 'UA', 'currency' => 'UAH'])
    ->setItem(['id' => 123123, 'name' => 'WooCommerce Store Sync'], 'FX-123456789') // license is optional
    ->send('flamix.solutions', 'client@example.com');
```

## Order

```php
$marketing->email()
    ->order('order_confirm', 9, ['items' => 999, 'delivery' => 100], 'UAH', 'Delivery address', 'https://shop.test/orders/9')
    ->setItem([
        ['url' => 'https://shop.test/p/1', 'img' => 'https://shop.test/p/1.webp', 'name' => 'Case', 'count' => 1, 'price' => 999],
    ])
    ->send('flamix.solutions', 'client@example.com');
```

## Choose

```php
$marketing->email()
    ->choose('Complaint', 'Decide what to do with <b>example.com</b>', [
        'https://app.test/ban'   => 'Ban',   // url => button label
        'https://app.test/allow' => 'Allow',
    ])
    ->send('flamix.solutions', 'admin@example.com');
```

## Payload

```php
// Body sent: template, then send()'s $data, then the setters, merged with array_merge_recursive.
$mail->setData(['title' => 'A'])->send('c', 'a@example.com', ['title' => 'B']); // title becomes ['B', 'A']
```

Form-encoded (`form_params`): `null` values are dropped, `true`/`false` become `1`/`0`.
