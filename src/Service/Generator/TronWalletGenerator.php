<?php

namespace App\Service\Generator;

use App\Dtos\WalletDataDTO;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Math\Math;
use Elliptic\EC;
use FurqanSiddiqui\BIP39\BIP39;
use FurqanSiddiqui\BIP39\Language\English;
use kornrunner\Keccak;
use Mdanter\Ecc\EccFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Tuupola\Base58;

class TronWalletGenerator implements WalletGeneratorInterface
{
    public function __construct(
        #[Autowire('%encryption_key%')]
        private readonly string $encryptionKey,
        private string $mnemonicDir = __DIR__,
    ) {
        $this->mnemonicDir = realpath(__DIR__ . '/../../../var/mnemonics');
    }

    public function generate(): WalletDataDTO
    {
        if (!$this->encryptionKey) {
            throw new \RuntimeException('Encryption key is required');
        }

        $mnemonic = BIP39::fromRandom(English::getInstance(), 12);
        $mnemonicPhrase = implode(' ', $mnemonic->words);

        $seedGenerator = new Bip39SeedGenerator();
        $seed = $seedGenerator->getSeed($mnemonicPhrase);

        $math = new Math();
        $generator = EccFactory::getSecgCurves()->generator256k1();

        $ecAdapter = new EcAdapter($math, $generator);

        $rootKey = HierarchicalKeyFactory::fromEntropy($seed, $ecAdapter);
        $derivedKey = $rootKey->derivePath("m/44'/195'/0'/0/0");
        $privateKeyHex = $derivedKey->getPrivateKey()->getHex();


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
            network: 'TRX',
            mnemonic: $filename
        );
    }

    public function calculatePK(string $privateKeyHex): string
    {
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($privateKeyHex);

        // 1. Get uncompressed public key, remove 0x04
        $publicKeyHex = $key->getPublic(false, 'hex');
        $publicKeyBin = hex2bin(substr($publicKeyHex, 2));

        // 2. Keccak256 hash
        $hash = Keccak::hash($publicKeyBin, 256);
        $addressHex = '41' . substr($hash, -40);

        // 3. Add checksum (SHA256 twice)
        $addressBin = hex2bin($addressHex);
        $checksum = substr(hash('sha256', hash('sha256', $addressBin, true), true), 0, 4);

        // 4. Append checksum to address
        $addressWithChecksum = $addressBin . $checksum;

        // 5. Base58 encode
        return $this->base58Encode($addressWithChecksum);
    }

    private function base58Encode(string $input): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        $intVal = gmp_import($input);

        $encoded = '';
        while (gmp_cmp($intVal, 0) > 0) {
            list($intVal, $rem) = gmp_div_qr($intVal, $base);
            $encoded = $alphabet[gmp_intval($rem)] . $encoded;
        }

        // Deal with leading zeros
        foreach (str_split($input) as $char) {
            if ($char === "\x00") {
                $encoded = '1' . $encoded;
            } else {
                break;
            }
        }

        return $encoded;
    }
}