@php /** @var \Laravel\Boost\Install\GuidelineAssist $assist */ @endphp
# Flamix Marketing PHP SDK

Plain PHP Guzzle client for the Flamix Marketing API: email (`pr.flamix.info`), SMS, short links and QR codes (`lnk.ua`, `g.flamix.info`). It has no service provider, config file, facade or queue; the app passes the token and the language itself.

- Do not call the marketing API with `Http::` or a hand-made Guzzle client: `new \Flamix\Marketing\Client($token, $lang)`, then `->email()`, `->link()`; SMS is `new \Flamix\Marketing\Actions\SMS($token, $company)`.
- Build each mail from `$client->email()`. The `$client->email` property is one shared instance and carries subject, body and data into the next `send()`.
- `send($company, $email, $data)` returns the API answer as an array and throws on any status outside 200–210 (own exceptions for 400, 401, 404, 422). Mail that must not break the caller goes in `try`/`catch (\Throwable)`.
- `Banner` is a stub whose `send()` calls `dd()`; do not use `$client->banner` or `banner()`.
- Tests: set `\Flamix\Marketing\Client::$handler` to a Guzzle `HandlerStack` over a `MockHandler`, and reset it to `null` in `tearDown()`.
- Full docs: `vendor/flamix/marketing-php-sdk/docs/*.md` (email, sms, links, errors).

Detail lives in the skill: `marketing-php-sdk-email`.
