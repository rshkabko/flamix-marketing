<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

class SMS
{
    use RequestsTrait;
    private string $company = 'default';

    public function __construct(string $token, string $company = 'default')
    {
        $this->token = $token;
        $this->company = $company;
    }

    /**
     * Sending SMS.
     *
     * @param string $phone
     * @param string $message
     * @param bool $isTest
     * @return array
     */
    public function send(string $phone, string $message, bool $isTest = false): array
    {
        return $this->post("sms/send/{$this->company}", [
            'phone' => $phone,
            'message' => $message,
            'test' => (int)$isTest,
        ]);
    }
}
