<?php

declare(strict_types=1);

namespace App\Application\Bus\Message;

use App\Domain\Wallet\Currency;

class CreateUserCommand
{
    private string $name;
    private Currency $currency;
    private string $country;
    private string $city;

    public function __construct(
        string $name,
        Currency $currency,
        string $country,
        string $city
    ) {
        $this->name = $name;
        $this->currency = $currency;
        $this->country = $country;
        $this->city = $city;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function country(): string
    {
        return $this->country;
    }

    public function city(): string
    {
        return $this->city;
    }
}
