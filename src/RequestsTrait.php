<?php

namespace Flamix\Marketing;

use GuzzleHttp\Client;
use Exception;

trait RequestsTrait
{
    protected string $token;
    protected string $uri = 'http://localhost/api/v1/';

    protected function request(string $method, string $uri, array $payload = [])
    {
        $response = new Client([
            'base_uri' => $this->uri,
            'headers' => [
                'Api-Token' => $apiToken,
                'User-Agent' => 'finolog/sdk-php'
            ]
        ])->request($method, $uri, 'form_params' => $payload);

        if (!$this->success($response))
            return $this->handleRequestError($response);

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    protected function get(string $uri)
    {
        return $this->request('GET', $uri);
    }

    protected function post(string $uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    protected function put(string $uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    protected function delete(string $uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    public function success($response): bool
    {
        if (!$response)
            return false;

        return (int) substr($response->getStatusCode(), 0, 1) === 2;
    }

    protected function handleRequestError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundException();
        }

        if ($response->getStatusCode() === 400) {
            throw new FailedActionException((string) $response->getBody());
        }

        if ($response->getStatusCode() === 401) {
            throw new UnauthorizedException((string) $response->getBody());
        }

        throw new Exception((string) $response->getBody());
    }
}