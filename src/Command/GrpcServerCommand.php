<?php

namespace App\Command;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Handler\WalletServiceHandler;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Grpc\Wallet\WalletServiceInterface;

#[AsCommand(
    name: 'grpc:serve',
    description: 'Start gRPC server on port 50051',
)]
    class GrpcServerCommand extends Command
{
    public function __construct(private WalletServiceHandler $walletHandler)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $worker = Worker::create();  // Створюємо worker з портом
        $server = new Server();

        $server->registerService(WalletServiceInterface::class, $this->walletHandler);

        $server->serve($worker);  // Передаємо worker, а не строку!

        return Command::SUCCESS;
    }
}