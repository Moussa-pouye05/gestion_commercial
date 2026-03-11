<?php
class Categorie{
    private ?int $id;
    private string $nom;

    public function __construct(?int $id,string $nom){
        $this->id = $id;
        $this->nom = $nom;
    }
    public function getId(){
        return $this->id;
    }
    public function getNom(){
        return $this->nom;
    }
}