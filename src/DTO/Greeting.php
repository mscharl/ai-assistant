<?php declare(strict_types=1);

namespace App\DTO;

use JsonSerializable;

class Greeting implements JsonSerializable
{
    public function __construct(
        public readonly string $short,
        public readonly string $long,
        public readonly array $request,
    ) {}


    public function jsonSerialize(): mixed
    {
        return [
            'short' => $this->short,
            'long' => $this->long,
        ];
    }
}
