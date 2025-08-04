<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $public_address = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $private_key = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $mnemonic_phrase = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'wallets')]
    private ?Network $network_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicAddress(): ?string
    {
        return $this->public_address;
    }

    public function setPublicAddress(string $public_address): static
    {
        $this->public_address = $public_address;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->private_key;
    }

    public function setPrivateKey(string $private_key): static
    {
        $this->private_key = $private_key;

        return $this;
    }

    public function getMnemonicPhrase(): ?string
    {
        return $this->mnemonic_phrase;
    }

    public function setMnemonicPhrase(?string $mnemonic_path): static
    {
        $this->mnemonic_phrase = $mnemonic_path;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getNetworkId(): ?Network
    {
        return $this->network_id;
    }

    public function setNetworkId(?Network $network_id): static
    {
        $this->network_id = $network_id;

        return $this;
    }
}
