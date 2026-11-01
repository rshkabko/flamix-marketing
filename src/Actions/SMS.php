<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

/** SMS sending; built directly, not through Client, and takes a company instead of a language. */
class SMS
{
    use RequestsTrait;
    private string $company = 'default';

    public function __construct(string $token, string $company = 'default')
    {
        $this->token = $token;
        $this->company = $company;
    }

    public function send(string $phone, string $message, bool $isTest = false): array
    {
        return $this->post("sms/send/{$this->company}", [
            'phone' => $phone,
            'message' => $message,
            'test' => (int)$isTest,
        ]);
    }
}
