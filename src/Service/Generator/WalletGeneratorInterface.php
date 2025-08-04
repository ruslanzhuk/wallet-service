<?php

namespace App\Service\Generator;

use App\Dtos\WalletDataDTO;

interface WalletGeneratorInterface
{
    public function generate(): WalletDataDTO;
}