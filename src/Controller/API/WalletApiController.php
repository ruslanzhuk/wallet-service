<?php

namespace App\Controller\API;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WalletApiController extends AbstractController
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    #[Route('/api/generate', name: 'generate_wallet', methods: ['POST'])]
    public function generate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $network = $data["network"] ?? null;

            if(!$network){
                return $this->json(['error' => 'network required'], 400);
            }

            $walletData = $this->walletService->generateWallet($network);

            return $this->json([
                'public_address' => $walletData->address,
                'private_key' => $walletData->privateKey,
                'network' => $walletData->network,
                'mnemonic' => $walletData->mnemonic,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }
}