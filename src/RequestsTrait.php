<?php

namespace Flamix\Marketing;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Flamix\Marketing\Exceptions\FailedActionException;
use Flamix\Marketing\Exceptions\UnauthorizedException;
use Flamix\Marketing\Exceptions\ValidationException;
use Flamix\Marketing\Exceptions\NotFoundException;
use Exception;

trait RequestsTrait
{
    protected string $token;
    protected string $uri = 'https://pr.flamix.info/api/v1/';

    /**
     * Make request to API endpoints.
     *
     * @param string $method POST, GET, etc
     * @param string $uri mail/send. Do not start with "/"
     * @param array $payload Additional datas
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(string $method, string $uri, array $payload = []): array
    {
        $response = (new Client([
            'base_uri' => $this->uri,
            'http_errors' => false,
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'flamix/api-sdk',
            ]
        ]))->request($method, $uri, [
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

    /**
     * Responce to array convertation.
     *
     * @param Response $response
     * @return array
     */
    private function json(Response $response): array
    {
        $result = json_decode((string)$response->getBody(), true);
        if (!json_last_error() && is_array($result)) {
            return $result;
        }

        return ['success' => false, 'data' => (string)$response->getBody()];
    }

    /**
     * Is our responce good?
     * Responce must return header status of 200.
     *
     * @param Response $response
     * @return bool
     */
    protected function success(Response $response): bool
    {
        if (!$response) return false;

        return ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 210);
    }

    /**
     * Handle errors.
     * We throw new exeptions with human explanations.
     *
     * @param Response $response
     * @return void
     * @throws Exception
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
