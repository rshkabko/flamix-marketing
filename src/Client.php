<?php

namespace Flamix\Marketing;

use Flamix\Marketing\RequestsTrait;
use Flamix\Marketing\Actions\Email;
use Flamix\Marketing\Actions\Banner;
use Flamix\Marketing\Actions\Link;

/** Entry point of the Flamix Marketing SDK: builds the Email, Banner and Link actions for one API token. */
class Client
{
    use RequestsTrait;

    /** @var callable|null Process-wide Guzzle handler for every SDK request; tests must reset it to null. */
    public static $handler = null;

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

    /** A fresh Email per mail, unlike the shared $email property, which keeps data between sends. */
    public function email(?string $type = null): Email
    {
        return new Email($this->token, $this->lang);
    }

    public function banner(int $width = 240, int $height = 800): Banner
    {
        return new Banner($this->token, $this->lang);
    }

    public function link(): Link
    {
        return new Link($this->token, $this->lang);
    }
}
