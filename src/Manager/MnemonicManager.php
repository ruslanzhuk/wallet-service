<?php

namespace App\Manager;

use App\Repository\WalletRepository;

class MnemonicManager
{

    public function __construct(
        private string $mnemonicDir,
        private string $encryptionKey,
        private WalletRepository $walletRepository,
    )
    {
    }

    public function getDecryptedMnemonic(int $walletId): string
    {
        $wallet = $this->walletRepository->findOneBy(['id' => $walletId]);

        if (!$wallet) {
            throw new \RuntimeException('Wallet not found');
        }

        $filePath = $this->mnemonicDir . '/' . $wallet->getMnemonicPath();

        if (!file_exists($filePath)) {
            throw new \RuntimeException('Mnemonic file not found');
        }

        $encryptedMnemonic = file_get_contents($filePath);
        $ivector = substr(hash('sha256', $this->encryptionKey), 0, 16);

        $decrypted = openssl_decrypt(
            $encryptedMnemonic,
            "AES-256-CBC",
            $this->encryptionKey,
            0,
            $ivector
        );

        if (!$decrypted) {
            throw new \RuntimeException('Mnemonic decryption failed');
        }

        return $decrypted;
    }

}