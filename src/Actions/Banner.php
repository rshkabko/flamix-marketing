<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

class Banner
{
    use RequestsTrait;

    public string $lang = 'en';

    public function __construct(string $token, string $lang = 'en')
    {
        $this->token = $token;
        $this->lang = $lang;

        return $this;
    }

    public function send(string $email, array $data = []): bool
    {
        dd('Sending email...');
    }

    public function banner(int $width = 240, int $height = 800): Banner
    {
        return new Banner($this->token, $this->lang, $width, $height);
    }
}
