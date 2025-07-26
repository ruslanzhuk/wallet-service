<?php

namespace App\Service\Generator;

use App\Dtos\WalletDataDTO;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use Elliptic\EC;
use FurqanSiddiqui\BIP39\BIP39;
use FurqanSiddiqui\BIP39\Language\English;
use kornrunner\Keccak;
use Mdanter\Ecc\EccFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EthereumWalletGenerator implements WalletGeneratorInterface
{
    public function __construct(
        #[Autowire('%encryption_key%')]
        private readonly string $encryptionKey,
        private string $mnemonicDir = __DIR__,
    )
    {
        $this->mnemonicDir = realpath(__DIR__ . '/../../../var/mnemonics');
    }


    public function generate(): WalletDataDTO
    {
        if (!$this->encryptionKey) {
            throw new \RuntimeException('Encryption key is required');
        }

        // Generation seed-phrase mnemonic
        $mnemonic = BIP39::fromRandom(English::getInstance(), 12);
        $mnemonicPhrase = implode(' ', $mnemonic->words);

        $seedGenerator = new Bip39SeedGenerator();
        $seed = $seedGenerator->getSeed($mnemonicPhrase);

        $math = new \BitWasp\Bitcoin\Math\Math();
        $generator = EccFactory::getSecgCurves()->generator256k1();

        $ecAdapter = new EcAdapter($math, $generator);

        $rootKey = HierarchicalKeyFactory::fromEntropy($seed, $ecAdapter);
        $derivedKey = $rootKey->derivePath("m/44'/60'/0'/0/0");
        $privateKeyHex = $derivedKey->getPrivateKey()->getHex();

        // Method 1, fked
//        $publicKey = $derivedKey->getPublicKey()->getHex();
//        $publicKeyBin = hex2bin(substr($publicKey, 2)); // Видаляємо префікс '04'

        // Method 2, fked
//        $publicKeyBin = $derivedKey->getPublicKey()->getBuffer()->getBinary();
//        $hash = Keccak::hash($publicKeyBin, 256);
//        $address = '0x' . substr($hash, -40);

        // Method 3. fked
//        $point = $derivedKey->getPublicKey()->getPoint();
//        $publicKeyHex = '04'
//            . str_pad(gmp_strval($point->getX(), 16), 64, '0', STR_PAD_LEFT)
//            . str_pad(gmp_strval($point->getY(), 16), 64, '0', STR_PAD_LEFT);
//        $publicKeyBin = hex2bin($publicKeyHex);

        $address = $this->calculatePK($privateKeyHex);

        $encryptedMnemonic = openssl_encrypt(
            $mnemonicPhrase,
            'AES-256-CBC',
            $this->encryptionKey,
            0,
            substr(hash('sha256', $this->encryptionKey), 0, 16)
        );

        if (!is_dir($this->mnemonicDir)) {
            mkdir($this->mnemonicDir, 0700, true);
        }
        $filename = uniqid('wallet_', true) . '.txt';
        file_put_contents($this->mnemonicDir . '/' . $filename, $encryptedMnemonic, 0600);

        return new WalletDataDTO(
            address: $address,
            privateKey: $privateKeyHex,
            network: 'ETH',
            mnemonic: $filename
        );
    }

    public function calculatePK(string $privateKeyHex): string
    {
        // 1. Create an EC key from a private key
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($privateKeyHex);

        // 2. Get public key
        $publicKey = $key->getPublic(false, 'hex');
        $publicKeyBin = hex2bin(substr($publicKey, 2)); // обрізаємо "04"

        $hash = Keccak::hash($publicKeyBin, 256);
        $address = '0x' . substr($hash, -40);
        $checksumAddress = $this->toChecksumAddress($address);

        return $checksumAddress;
    }

    private function toChecksumAddress(string $address): string
    {
        $address = strtolower(str_replace('0x', '', $address));
        $hash = Keccak::hash($address, 256);
        $checksum = '';

        for ($i = 0; $i < 40; $i++) {
            $char = $address[$i];
            $checksum .= (hexdec($hash[$i]) >= 8) ? strtoupper($char) : $char;
        }

        return '0x' . $checksum;
    }

}