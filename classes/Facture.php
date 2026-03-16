<?php
class Facture
{
    private ?int $id;
    private string $numero_facture;
    private string $date_facture;
    private int $id_commande;
    private float $total;
    private string $statut;

    public function __construct(
        ?int $id,
        string $numero_facture,
        string $date_facture,
        int $id_commande,
        float $total,
        string $statut
    ) {
        $this->id = $id;
        $this->numero_facture = $numero_facture;
        $this->date_facture = $date_facture;
        $this->id_commande = $id_commande;
        $this->total = $total;
        $this->statut = $statut;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroFacture(): string
    {
        return $this->numero_facture;
    }

    public function getDateFacture(): string
    {
        return $this->date_facture;
    }

    public function getIdCommande(): int
    {
        return $this->id_commande;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNumeroFacture(string $numero_facture): void
    {
        $this->numero_facture = $numero_facture;
    }

    public function setDateFacture(string $date_facture): void
    {
        $this->date_facture = $date_facture;
    }

    public function setIdCommande(int $id_commande): void
    {
        $this->id_commande = $id_commande;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}

