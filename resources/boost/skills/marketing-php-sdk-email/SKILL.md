---
name: marketing-php-sdk-email
description: "Flamix marketing-php-sdk mail, SMS and links. Use when sending mail through Flamix\\Marketing\\Client (email(), custom(), license(), payment(), order(), choose(), send()), Actions\\SMS, or link()->lnk()/qr()/get(), or when faking them in tests with Client::$handler. Also use for the symptoms: a mail arrives with the subject or body of the previous one, a mail goes out in the wrong language, the template cannot be overridden from send(), catch (\\Exception) misses an error from Link, a test hits the real API. Skip for app-side mail classes (Laravel Mail, notifications) that do not use this SDK."
---

# Marketing SDK

`Flamix\Marketing\Client` holds the token and the language and builds the `Email` and `Link` actions; `Actions\SMS` is built on its own.

Full docs: vendor/flamix/marketing-php-sdk/docs/email.md, sms.md, links.md, errors.md. Below is what the docs omit.

## Traps

- In a singleton (`app()->singleton(Client::class, …)`) the shared `$client->email` property leaks one user's mail data into the next user's mail. Always call `email()`.
- `email($type)` and `banner($width, $height)` accept arguments and ignore them.
- Language for one mail: `$mail = $client->email(); $mail->lang = 'uk';`. Changing `$client->lang` affects later `email()` calls, never the `$client->email` property.
- `send(…, ['template' => 'x'])` sends `template` as an array instead of overriding it; use `custom('x', …)` or `setTemplate('x')`.
- `Link::get()`, `lnk()` and `qr()` throw an Error (TypeError on PHP 8), not an `Exception`, when the error answer has no message, so `catch (\Exception)` misses it; catch `\Throwable`.

## Tests without network

```php
use GuzzleHttp\{HandlerStack, Middleware, Handler\MockHandler, Psr7\Response};

$history = [];
$stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"success":true}')]));
$stack->push(Middleware::history($history));
\Flamix\Marketing\Client::$handler = $stack; // process-wide: reset to null in tearDown()

parse_str((string) $history[0]['request']->getBody(), $body); // form-encoded, not JSON
```

`Client::$handler` applies to every action, `SMS` and `Link` included.
