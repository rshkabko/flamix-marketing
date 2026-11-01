# Errors

Email and SMS calls return the decoded JSON as an array. Any status from 200 to 210 is a success.

```php
use Flamix\Marketing\Exceptions\FailedActionException; // 400, message holds the response
use Flamix\Marketing\Exceptions\UnauthorizedException; // 401, wrong or missing token
use Flamix\Marketing\Exceptions\NotFoundException;     // 404
use Flamix\Marketing\Exceptions\ValidationException;   // 422, message holds the errors

try {
    $marketing->email()->custom('license', $data)->send('flamix.solutions', $email);
} catch (ValidationException $e) {
    $e->getCode(); // 422
} catch (\Exception $e) {
    // any other status: plain Exception with the response printed in the message
}
```

```php
// 2xx with a body that is not JSON: no exception
['success' => false, 'data' => '<raw body>']
```

Timeouts: 10 s to connect, 10 s in total. Network errors are Guzzle exceptions (`GuzzleHttp\Exception\GuzzleException`).
