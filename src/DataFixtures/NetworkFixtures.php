<?php

namespace App\DataFixtures;

use App\Entity\Network;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NetworkFixtures extends Fixture
{
    // Додаємо константи для референсів, щоб використовувати їх у CryptoCurrencyFixture
    public const NETWORK_BTC = 'network_bitcoin';
    public const NETWORK_ETH = 'network_ethereum';
    public const NETWORK_SOL = 'network_solana';
    public const NETWORK_TRX = 'network_tron';
    public const NETWORK_BSC = 'network_bsc';
    public const NETWORK_DOGE = 'network_dogecoin';

    public function load(ObjectManager $manager): void
    {
        $networks = [
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'explorer_url' => 'https://blockchain.com/btc',
                'reference' => self::NETWORK_BTC,
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',
                'explorer_url' => 'https://etherscan.io',
                'reference' => self::NETWORK_ETH,
            ],
            [
                'code' => 'SOL',
                'name' => 'Solana',
                'explorer_url' => 'https://solscan.io',
                'reference' => self::NETWORK_SOL,
            ],
            [
                'code' => 'TRX',
                'name' => 'Tron',
                'explorer_url' => 'https://tronscan.org',
                'reference' => self::NETWORK_TRX,
            ],
            [
                'code' => 'BSC',
                'name' => 'Binance Smart Chain',
                'explorer_url' => 'https://bscscan.com',
                'reference' => self::NETWORK_BSC,
            ],
            [
                'code' => 'DOGE',
                'name' => 'Dogecoin',
                'explorer_url' => 'https://dogechain.info',
                'reference' => self::NETWORK_DOGE,
            ],
        ];

        foreach ($networks as $networkData) {
            $network = new Network();
            $network->setCode($networkData['code']);
            $network->setName($networkData['name']);
            $network->setExplorerUrl($networkData['explorer_url']);

            $manager->persist($network);
            // Додаємо референс для використання в CryptoCurrencyFixture
            $this->addReference($networkData['reference'], $network);
        }

        $manager->flush();
    }
}