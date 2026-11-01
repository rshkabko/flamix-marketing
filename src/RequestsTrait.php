<?php

namespace Flamix\Marketing;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Flamix\Marketing\Exceptions\FailedActionException;
use Flamix\Marketing\Exceptions\UnauthorizedException;
use Flamix\Marketing\Exceptions\ValidationException;
use Flamix\Marketing\Exceptions\NotFoundException;
use Exception;

/** Guzzle calls to the Flamix Marketing API: auth header, form body, HTTP status to exception. */
trait RequestsTrait
{
    protected string $token;
    protected string $uri = 'https://pr.flamix.info/api/v1/';

    /**
     * @param string $uri relative, without a leading "/", or base_uri loses its /api/v1/ path
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    protected function request(string $method, string $uri, array $payload = []): array
    {
        $response = (new Client([
            'base_uri' => $this->uri,
            'handler' => \Flamix\Marketing\Client::$handler,
            'http_errors' => false,
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'flamix/api-sdk',
            ]
        ]))->request($method, $uri, [
            // Form-encoded despite the JSON header: Guzzle replaces it, and null values are dropped.
            'form_params' => $payload,
            'connect_timeout' => 10,
            'timeout' => 10,
        ]);

        if (!$this->success($response))
            return $this->handleRequestError($response);

        return $this->json($response);
    }

    protected function get(string $uri): array
    {
        return $this->request('GET', $uri);
    }

    protected function post(string $uri, array $payload = []): array
    {
        return $this->request('POST', $uri, $payload);
    }

    protected function put(string $uri, array $payload = []): array
    {
        return $this->request('PUT', $uri, $payload);
    }

    protected function delete(string $uri, array $payload = []): array
    {
        return $this->request('DELETE', $uri, $payload);
    }

    private function json(Response $response): array
    {
        $result = json_decode((string)$response->getBody(), true);
        if (!json_last_error() && is_array($result)) {
            return $result;
        }

        return ['success' => false, 'data' => (string)$response->getBody()];
    }

    protected function success(Response $response): bool
    {
        if (!$response) return false;

        return ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 210);
    }

    /**
     * Always throws: 400, 401, 404 and 422 get their own exception, anything else a plain Exception.
     *
     * @throws \Exception
     */
    protected function handleRequestError(Response $response): void
    {
        switch ($response->getStatusCode()) {
            case 400:
                throw new FailedActionException($this->json($response));

            case 401:
                throw new UnauthorizedException();

            case 404:
                throw new NotFoundException();

            case 422:
                throw new ValidationException($this->json($response));

            default:
                throw new Exception(print_r($this->json($response), true));
        }
    }
}
