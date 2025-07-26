<?php


namespace App\DataFixtures;

use App\Entity\CryptoCurrency;
use App\Entity\Network;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CryptoCurrencyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cryptoCurrencies = [
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'network_ref' => NetworkFixtures::NETWORK_BTC,
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',
                'network_ref' => NetworkFixtures::NETWORK_ETH,
            ],
            [
                'code' => 'USDT',
                'name' => 'Tether (ERC20)',
                'network_ref' => NetworkFixtures::NETWORK_ETH,
            ],
            [
                'code' => 'SOL',
                'name' => 'Solana',
                'network_ref' => NetworkFixtures::NETWORK_SOL,
            ],
            [
                'code' => 'TRX',
                'name' => 'Tron',
                'network_ref' => NetworkFixtures::NETWORK_TRX,
            ],
            [
                'code' => 'BNB',
                'name' => 'Binance Coin',
                'network_ref' => NetworkFixtures::NETWORK_BSC,
            ],
            [
                'code' => 'DOGE',
                'name' => 'Dogecoin',
                'network_ref' => NetworkFixtures::NETWORK_DOGE,
            ],
        ];

        foreach ($cryptoCurrencies as $cryptoData) {
            $cryptoCurrency = new CryptoCurrency();
            $cryptoCurrency->setCode($cryptoData['code']);
            $cryptoCurrency->setName($cryptoData['name']);
            $cryptoCurrency->setNetworkId($this->getReference($cryptoData['network_ref'], Network::class));

            $manager->persist($cryptoCurrency);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NetworkFixtures::class,
        ];
    }
}