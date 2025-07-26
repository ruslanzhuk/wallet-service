<?php

namespace App\Dtos;

class WalletDataDTO
{
    public function __construct(
        public readonly string $address,
        public readonly string $privateKey,
        public readonly string $network,
        public readonly ?string $mnemonic = null,
    )
    {}
}