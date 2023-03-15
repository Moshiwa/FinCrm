<?php

namespace App\Services\Sender;

use App\Enums\IntegrationEnum;

abstract class SenderService
{
    protected string $recipient;

    protected string $error = '';

    protected string $title = '';

    abstract public function send($message);

    public static function factory($integration_name, $recipient): ?SenderService
    {
        $integration = IntegrationEnum::fromValue($integration_name);
        if (empty($integration)) {
            return null;
        }

        $integration = IntegrationEnum::getEntity($integration->value);

        return new $integration($recipient);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
