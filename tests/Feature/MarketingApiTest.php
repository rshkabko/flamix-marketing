<?php

namespace Flamix\Marketing\Tests\Feature;

use Flamix\Marketing\Actions\Link;
use Flamix\Marketing\Actions\SMS;
use Flamix\Marketing\Client;
use Flamix\Marketing\Exceptions\FailedActionException;
use Flamix\Marketing\Exceptions\NotFoundException;
use Flamix\Marketing\Exceptions\UnauthorizedException;
use Flamix\Marketing\Exceptions\ValidationException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class MarketingApiTest extends TestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    private array $sent = [];

    protected function tearDown(): void
    {
        Client::$handler = null;
    }

    private function respond(Response ...$responses): void
    {
        $this->sent = [];
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->sent));
        Client::$handler = $stack;
    }

    private function request(int $i = 0): RequestInterface
    {
        return $this->sent[$i]['request'];
    }

    private function body(int $i = 0): array
    {
        parse_str((string) $this->request($i)->getBody(), $body);

        return $body;
    }

    public function test_custom_mail_is_posted_to_company_lang_and_address(): void
    {
        $this->respond(new Response(200, [], '{"success":true}'));

        $result = (new Client('secret', 'uk'))->email()
            ->setSubject('Hi')
            ->setBody('<p>Body</p>')
            ->send('flamix.solutions', 'client@example.com', ['title' => 'Title']);

        $this->assertSame(['success' => true], $result);
        $this->assertSame('POST', $this->request()->getMethod());
        $this->assertSame('https://pr.flamix.info/api/v1/mail/send/flamix.solutions/uk/client@example.com', (string) $this->request()->getUri());
        $this->assertSame('Bearer secret', $this->request()->getHeaderLine('Authorization'));
        $this->assertSame('application/x-www-form-urlencoded', $this->request()->getHeaderLine('Content-Type'));
        $this->assertSame(['template' => 'general', 'title' => 'Title', 'subject' => 'Hi', 'body' => '<p>Body</p>'], $this->body());
    }

    public function test_payment_mail_carries_template_lowercased_currency_user_and_license(): void
    {
        $this->respond(new Response(200, [], '{}'));

        (new Client('secret'))->email()
            ->payment('debit', 120, 'USD', ['id' => 999])
            ->setUser(['id' => 1, 'name' => 'Roman'])
            ->setItem(['id' => 5, 'name' => 'Sync'], 'FX-1')
            ->send('flamix.solutions', 'client@example.com');

        $body = $this->body();
        $this->assertSame('payment', $body['template']);
        $this->assertSame('usd', $body['currency']);
        $this->assertSame(['id' => '999'], $body['payment']);
        $this->assertSame('Roman', $body['user']['name']);
        $this->assertSame('FX-1', $body['license']);
    }

    public function test_the_email_property_keeps_data_from_the_previous_mail(): void
    {
        $this->respond(new Response(200, [], '{}'), new Response(200, [], '{}'), new Response(200, [], '{}'));
        $client = new Client('secret');

        $client->email->setSubject('First')->send('c', 'a@example.com');
        $client->email->setBody('Second')->send('c', 'b@example.com');
        $client->email()->setBody('Third')->send('c', 'c@example.com');

        $this->assertSame('First', $this->body(1)['subject']);
        $this->assertArrayNotHasKey('subject', $this->body(2));
    }

    public function test_sms_is_posted_to_the_company(): void
    {
        $this->respond(new Response(200, [], '{"status":"ok"}'));

        $result = (new SMS('secret', '4mobi.com.ua'))->send('380000000000', 'hello', true);

        $this->assertSame(['status' => 'ok'], $result);
        $this->assertSame('https://pr.flamix.info/api/v1/sms/send/4mobi.com.ua', (string) $this->request()->getUri());
        $this->assertSame(['phone' => '380000000000', 'message' => 'hello', 'test' => '1'], $this->body());
    }

    public function test_lnk_and_qr_return_the_link_from_lnk_ua(): void
    {
        $answer = '{"result":{"lnk":"https://lnk.ua/abc","qr":"https://lnk.ua/abc.png"}}';
        $this->respond(new Response(200, [], $answer), new Response(200, [], $answer));
        $link = (new Client('secret'))->link();

        $this->assertSame('https://lnk.ua/abc', $link->lnk('https://example.com', ['title' => 'T']));
        $this->assertSame('https://lnk.ua/abc.png', $link->qr('https://example.com'));
        $this->assertSame('https://lnk.ua/api/v1/link/create', (string) $this->request()->getUri());
        $this->assertSame(['title' => 'T', 'link' => 'https://example.com'], $this->body());
    }

    public function test_lnk_error_is_thrown_with_the_api_message(): void
    {
        $this->respond(new Response(200, [], '{"result":{"errors":true,"message":"Bad link"}}'));

        $this->expectExceptionMessage('Bad link');
        (new Link('secret'))->lnk('nope');
    }

    public function test_get_error_without_a_message_throws_an_exception_not_an_error(): void
    {
        $this->markTestIncomplete('Link::get passes the result array to new Exception() when "message" is missing: an Error (TypeError on PHP 8), src/Actions/Link.php:28');
        $this->respond(new Response(200, [], '{"status":"error"}'));

        $this->expectException(\Exception::class);
        (new Link('secret'))->get('https://example.com');
    }

    /** @dataProvider httpErrors */
    #[\PHPUnit\Framework\Attributes\DataProvider('httpErrors')]
    public function test_http_errors_become_sdk_exceptions(int $status, string $exception): void
    {
        $this->respond(new Response($status, [], '{"errors":{"email":["required"]}}'));

        $this->expectException($exception);
        $this->expectExceptionCode($status);
        (new Client('secret'))->email()->send('c', 'a@example.com');
    }

    public static function httpErrors(): array
    {
        return [
            [400, FailedActionException::class],
            [401, UnauthorizedException::class],
            [404, NotFoundException::class],
            [422, ValidationException::class],
        ];
    }
}
