<?php

namespace App\Service;

use App\Dtos\WalletDataDTO;
use App\Entity\Wallet;
use App\Repository\NetworkRepository;
use App\Repository\WalletRepository;
use App\Resolver\WalletGeneratorResolver;
use Doctrine\ORM\EntityManagerInterface;

class WalletService
{
    public function __construct(
        private WalletGeneratorResolver $resolver,
        private EntityManagerInterface $em,
        private NetworkRepository $networkRepository,
        private WalletRepository $walletRepository,
    ){}

    public function generateWallet(string $network): WalletDataDTO {
        $generator = $this->resolver->resolve($network);

        $walletData = $generator->generate();

        $existingWallet = $this->walletRepository->findOneBy(['public_address' => $walletData->address]);
        if ($existingWallet) {
            throw new \RuntimeException("Wallet address already exists: {$walletData->address}");
        }

        $wallet = new Wallet();
        $wallet->setPublicAddress($walletData->address);
        $wallet->setPrivateKey($walletData->privateKey);
        $wallet->setNetworkId($this->networkRepository->findOneBy(['code' => $walletData->network]));
        $wallet->setCreatedAt(new \DateTimeImmutable());
        $wallet->setMnemonicPhrase($walletData->mnemonic);

        $this->em->persist($wallet);
        $this->em->flush();

        return $walletData;
    }
}