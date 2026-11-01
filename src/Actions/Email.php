<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

/** Mail through pr.flamix.info: pick a template with a builder method, then send(). */
class Email
{
    use RequestsTrait;

    public string $lang = 'en';
    private string $template = 'general';
    private array $data = [];

    public function __construct(string $token, string $lang = 'en')
    {
        $this->token = $token;
        $this->lang = $lang;

        return $this;
    }

    /** Template the API renders; "general" by default. */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /** Merged into the data set before; send() merges it again with its own $data. */
    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->data['subject'] = $subject;
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->data['body'] = $body;
        return $this;
    }

    /** @param array{id?: int, name?: string, region?: string, currency?: string} $user */
    public function setUser(array $user): self
    {
        $this->data['user'] = $user;
        return $this;
    }

    /** @param array $item ['id' => 123123, 'name' => '…'], or a list of order lines for order() */
    public function setItem(array $item, ?string $license = null): self
    {
        $this->data['item'] = $item;

        if ($license)
            $this->data['license'] = $license;

        return $this;
    }

    private function prepareData(array $data = []): array
    {
        // array_merge_recursive: a key set both here and in send() is sent as an array of both values.
        return array_merge_recursive([
            'template' => $this->template,
        ], $data, $this->data);
    }

    public function send(string $company, string $email, array $data = []): array
    {
        return $this->post("mail/send/{$company}/{$this->lang}/{$email}", $this->prepareData($data));
    }

    public function custom(string $template, array $data): self
    {
        $this->setTemplate($template);
        $this->setData($data);

        return $this;
    }

    public function license(string $license, string $name): self
    {
        $this->custom('license', [
            'license' => $license,
            'name' => $name,
        ]);

        return $this;
    }

    /**
     * @param string $type debit or credit
     * @param array $payment ['id' => 999, 'date' => '10/12/2022', 'method' => 'Flamix.Kassa']
     */
    public function payment(string $type, float $amount, string $currency = 'USD', array $payment = []): self
    {
        $this->custom('payment', [
            'type' => $type,
            'amount' => $amount,
            'currency' => strtolower($currency),
        ]);

        if (!empty($payment))
            $this->setData(['payment' => $payment]);

        return $this;
    }

    /**
     * @param string $type the template, e.g. order_confirm
     * @param array $prices ['items' => 999, 'delivery' => 100]
     * @param string $status link to the order status page
     */
    public function order(string $type, $id, array $prices, string $currency, string $address = '', string $status = ''): self
    {
        $this->custom($type, [
            'order' => [
                'id' => $id,
                'price' => $prices,
                'currency' => $currency,
                'address' => $address,
                'status' => $status,
            ]
        ]);

        return $this;
    }

    /** @param array<string, string> $options url => button label */
    public function choose(string $title, string $body, array $options): self
    {
        $this->custom('choose', [
            'title' => $title,
            'body' => $body,
            'options' => $options,
        ]);

        return $this;
    }
}
