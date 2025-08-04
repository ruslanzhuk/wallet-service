<?php

namespace App\Handler;

use App\Service\WalletService;
use Spiral\RoadRunner\GRPC;
use Spiral\RoadRunner\GRPC\Exception\InvokeException;
use Grpc\Wallet\GenerateWalletRequest;
use Grpc\Wallet\GenerateWalletResponse;
use Grpc\Wallet\WalletServiceInterface;

class WalletServiceHandler implements WalletServiceInterface
{
    public function __construct(private WalletService $walletService) {}

    public function GenerateWallet(GRPC\ContextInterface $ctx, GenerateWalletRequest $in): GenerateWalletResponse
    {
        $network = $in->getNetwork();

        try {
            $walletData = $this->walletService->generateWallet($network);

            $response = new GenerateWalletResponse();
            $response->setPublicAddress($walletData->address);
            $response->setPrivateKey($walletData->privateKey);
            $response->setNetwork($network);
            $response->setMnemonic($walletData->mnemonic);

            return $response;
        } catch (\Throwable $e) {
            throw new InvokeException("Failed to generate wallet: " . $e->getMessage());
        }
    }
}