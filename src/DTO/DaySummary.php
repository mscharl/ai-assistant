<?php declare(strict_types=1);

namespace App\DTO;

use JsonSerializable;

class DaySummary implements JsonSerializable
{
    public function __construct(public readonly string $content, public readonly array $request)
    {
    }


    public function jsonSerialize(): mixed
    {
        return [
            'content' => $this->content
        ];
    }
}
