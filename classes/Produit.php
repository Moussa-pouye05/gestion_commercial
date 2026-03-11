<?php
class Produit
{
    private ?int $id;
    private string $profile;
    private string $nom;
    private float $prix_vente;
    private float $prix_achat;
    private int $quantite;
    private string $code_barre;
    private int $catId;

    public function __construct(
        ?int $id,
        string $profile,
        string $nom,
        float $prix_vente,
        float $prix_achat,
        int $quantite,
        int $catId,
        string $code_barre
    ) {
        $this->id = $id;
        $this->profile = $profile;
        $this->nom = $nom;
        $this->prix_vente = $prix_vente;
        $this->prix_achat = $prix_achat;
        $this->quantite = $quantite;
        $this->code_barre = $code_barre;
        $this->catId = $catId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): string
    {
        return $this->profile;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrixVente(): float
    {
        return $this->prix_vente;
    }

    public function getPrixAchat(): float
    {
        return $this->prix_achat;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getCodeBarre(): string
    {
        return $this->code_barre;
    }

    public function getCatId(): int
    {
        return $this->catId;
    }
}
