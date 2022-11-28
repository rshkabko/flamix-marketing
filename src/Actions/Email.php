<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

class Email
{
    use RequestsTrait;

    public string $lang = 'en';

    public function __construct(string $token, string $lang = 'en')
    {
        $this->token = $token;
        $this->lang = $lang;

        return $this;
    }

    public function send(string|int $company, string $email, array $data = []): bool
    {
        return $this->post("mail/send/{$company}/{$this->lang}/{$email}/", $data);
    }

    public function banner(int $width = 240, int $height = 800): Banner
    {
        return new Banner($this->token, $this->lang, $width, $height);
    }
}
