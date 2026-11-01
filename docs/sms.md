# SMS

Built directly, not through `Client`. The second argument is the company, not a language.

```php
use Flamix\Marketing\Actions\SMS;

$sms = new SMS('token', '4mobi.com.ua'); // POST sms/send/4mobi.com.ua

$result = $sms->send('380000000000', 'hello!');       // array: the API response
$result = $sms->send('380000000000', 'hello!', true); // test send: "test" => 1
```
