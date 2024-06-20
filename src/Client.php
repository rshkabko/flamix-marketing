<?php

namespace Flamix\Marketing;

use Flamix\Marketing\RequestsTrait;
use Flamix\Marketing\Actions\Email;
use Flamix\Marketing\Actions\Banner;
use Flamix\Marketing\Actions\Link;

class Client
{
    use RequestsTrait;

    public string $lang = 'en';
    public Email $email;
    public Banner $banner;

    public function __construct(string $token, string $lang = 'en')
    {
        $this->token = $token;
        $this->lang = $lang;

        $this->email = new Email($token, $lang);
        $this->banner = new Banner($token, $lang);

        return $this;
    }

    public function email(?string $type = null): Email
    {
        return new Email($this->token, $this->lang, $type);
    }

    public function banner(int $width = 240, int $height = 800): Banner
    {
        return new Banner($this->token, $this->lang, $width, $height);
    }

    public function link(): Link
    {
        return new Link($this->token, $this->lang);
    }
}
