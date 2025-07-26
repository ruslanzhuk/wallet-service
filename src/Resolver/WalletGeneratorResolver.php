<?php

namespace App\Resolver;

use App\Service\Generator\EthereumWalletGenerator;
use App\Service\Generator\TronWalletGenerator;
use App\Service\Generator\WalletGeneratorInterface;

class WalletGeneratorResolver
{
    public function __construct(
        private EthereumWalletGenerator $eth,
        private TronWalletGenerator $tron,
    ) {}

    public function resolve(string $network): WalletGeneratorInterface
    {
        return match (strtoupper($network)) {
            'ETH' => $this->eth,
            'TRX' => $this->tron,
            default => throw new \InvalidArgumentException("Unsupported network: $network")
        };
    }
}