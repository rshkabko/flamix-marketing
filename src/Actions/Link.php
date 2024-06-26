<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

class Link
{
    use RequestsTrait;

    public string $lang = 'en';

    public function __construct(string $token, string $lang = 'en')
    {
        $this->token = $token;
        $this->lang = $lang;

        return $this;
    }

    public function get(string $url, array $data = []): string
    {
        // Special URL for the API
        $this->uri = 'https://g.flamix.info/api/v1/';
        $result = $this->post("link", array_merge($data, ['url' => $url]));

        if (($result['status'] ?? 'error') !== 'success') {
            throw new Exception($result['message'] ?? $result);
        }

        return $result['link'];
    }
}
