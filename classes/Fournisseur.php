<?php
class Fournisseur{
    private ?int $id;
    private string $nom;
    private string $telephone;
    private string $adresse;

    public function __construct(?int $id, string $nom, string $telephone, string $adresse){
        $this->id = $id;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
    }

    public function getId(){
        return $this->id;
    }
    public function getNom(){
        return $this->nom;
    }
    public function getTelephone(){
        return $this->telephone;
    }
    public function getAdresse(){
        return $this->adresse;
    }
}