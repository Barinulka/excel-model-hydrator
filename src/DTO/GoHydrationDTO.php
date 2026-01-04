<?php

namespace App\DTO;

class GoHydrationDTO
{
    public function __construct(
        public readonly string $template,
        public readonly array $data
    ) {
    }
}
