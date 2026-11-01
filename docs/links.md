# Links

Short links and QR codes. Each method talks to its own host, not to `pr.flamix.info`.

```php
use Flamix\Marketing\Client as FlamixMarketing;

$link = (new FlamixMarketing('token'))->link();

// lnk.ua: POST https://lnk.ua/api/v1/link/create, the url is sent as "link"
$short = $link->lnk('https://example.com/long', ['title' => 'Optional']); // ?string
$qr    = $link->qr('https://example.com/long');                           // ?string, QR image URL

// g.flamix.info: POST https://g.flamix.info/api/v1/link, the url is sent as "url"
$short = $link->get('https://example.com/long'); // string, throws unless "status" is "success"
```

`lnk()` and `qr()` throw `Exception` with the API's `result.message` when `result.errors` is set.
