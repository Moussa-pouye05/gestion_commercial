<?php
class Commande
{
    private ?int $id;
    private string $date_commande;
    private int $id_user;
    private string $etat;
    private int $id_client;
    private float $total;

    public function __construct(
        ?int $id,
        string $date_commande,
        int $id_user,
        string $etat,
        int $id_client,
        float $total
    ) {
        $this->id = $id;
        $this->date_commande = $date_commande;
        $this->id_user = $id_user;
        $this->etat = $etat;
        $this->id_client = $id_client;
        $this->total = $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): string
    {
        return $this->date_commande;
    }

    public function getIdUser(): int
    {
        return $this->id_user;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function getIdClient(): int
    {
        return $this->id_client;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDateCommande(string $date_commande): void
    {
        $this->date_commande = $date_commande;
    }

    public function setIdUser(int $id_user): void
    {
        $this->id_user = $id_user;
    }

    public function setEtat(string $etat): void
    {
        $this->etat = $etat;
    }

    public function setIdClient(int $id_client): void
    {
        $this->id_client = $id_client;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }
}

