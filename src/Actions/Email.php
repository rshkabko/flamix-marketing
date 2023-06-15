<?php

namespace Flamix\Marketing\Actions;

use Flamix\Marketing\RequestsTrait;

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

    /**
     * Set template.
     * If we want send custom mail.
     *
     * @param string $subject
     * @return $this
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set Data.
     *
     * @param string $subject
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Set mail subject.
     * Will displayed in servece fields
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->data['subject'] = $subject;
        return $this;
    }

    /**
     * Set body with HTML tags.
     *
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->data['body'] = $body;
        return $this;
    }

    /**
     * Set User info.
     *
     * @param array $user ['id' => 1, 'name' => 'Roman Shkabko', 'region' => 'UA', 'currency' => 'UAH']
     * @return $this
     */
    public function setUser(array $user): self
    {
        $this->data['user'] = $user;
        return $this;
    }

    /**
     * Set item info.
     *
     * @param array $item ['id' => 123123, 'name' => 'WooCommerce Store Sync']
     * @param string|null $license FX-1234567890
     * @return $this
     */
    public function setItem(array $item, ?string $license = null): self
    {
        $this->data['item'] = $item;

        if ($license)
            $this->data['license'] = $license;

        return $this;
    }

    /**
     * Prepare main data (like template) and input data.
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data = []): array
    {
        return array_merge_recursive([
            'template' => $this->template,
        ], $data, $this->data);
    }

    /**
     * Sending email.
     * Main function to sending email. We can pass any params and configurate email type.
     *
     * @param string $company
     * @param string $email
     * @param array $data
     * @return array
     */
    public function send(string $company, string $email, array $data = []): array
    {
        return $this->post("mail/send/{$company}/{$this->lang}/{$email}", $this->prepareData($data));
    }

    /**
     * Sending custom email.
     *
     * @param string $template
     * @param array $data
     * @return $this
     */
    public function custom(string $template, array $data): self
    {
        $this->setTemplate($template);
        $this->setData($data);

        return $this;
    }


    /**
     * Sending license key.
     *
     * @param string $license FX-1234567890
     * @param string $name WooCommerce Store Sync
     * @return $this
     */
    public function license(string $license, string $name): self
    {
        $this->setTemplate('license');
        $this->data['license'] = $license;
        $this->data['name'] = $name;

        return $this;
    }

    /**
     * Mail when user make payment or we charging from account.
     *
     * @param string $type debit or credit
     * @param float $amount 100
     * @param string $currency USD
     * @param array $payment ['id' => 999, 'date' => '10/12/2022', 'method' => 'Flamix.Kassa']
     * @return $this
     */
    public function payment(string $type, float $amount, string $currency = 'USD', array $payment = []): self
    {
        $this->setTemplate('payment');
        $this->data['type'] = $type;
        $this->data['amount'] = $amount;
        $this->data['currency'] = strtolower($currency);

        if (!empty($payment))
            $this->data['payment'] = $payment;

        return $this;
    }

    /**
     * Order template.
     *
     * @param string $type ex, confirm_order
     * @param $id Order Number
     * @param array $prices Many prices ['items' => 999, 'delivery' => 100]
     * @param string $currency UAH or USD
     * @param string $address Full delivery address
     * @param string $status Link to check status
     * @return $this
     */
    public function order(string $type, $id, array $prices, string $currency, string $address = '', string $status = ''): self
    {
        $this->setTemplate($type);
        $this->data['order'] = [
            'id' => $id,
            'price' => $prices,
            'currency' => $currency,
            'address' => $address,
            'status' => $status,
        ];

        return $this;
    }
}
