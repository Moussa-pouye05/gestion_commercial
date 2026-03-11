<?php
class User{
    private ?int $id;
    private string $nom;
    private string $email;
    private string $password;
    private string $profile;
    private string $telephone;
    private string $role;

    public function __construct(?int $id, string $profile, string $nom, string $email, string $password, string $telephone,string $role){
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->profile = $profile;
        $this->telephone = $telephone;
        $this->role = $role;
    }
    public function getId(){
    return $this->id;
    }
    public function getNom(){
        return $this->nom;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getProfile(){
        return $this->profile;
    }
    public function getTelephone(){
        return $this->telephone;
    }
    public function getRole(){
        return $this->role;
    }
}