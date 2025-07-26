<?php

namespace App\Controller;

use App\Manager\MnemonicManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class WalletController extends AbstractController
{
    #[Route('/wallet/{id}/mnemonic', name: 'wallet_mnemonic', methods: ['GET'])]
    public function show(int $id, MnemonicManager $mnemonicManager): Response
    {
        try {
            $mnemonic = $mnemonicManager->getDecryptedMnemonic($id);
            return $this->render('wallet/show_mnemonic.html.twig', [
                'mnemonic' => $mnemonic,
                'words' => explode(' ', $mnemonic),
            ]);
        } catch (\Throwable $e) {
            return $this->render('wallet/show_mnemonic.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
