<?php 


class UserModel{
    /**
     *  @var int
     */
    public $id;

    /**
     *  @var string
     */
        public $mail;
        
    /**
     *  @var string
     */
    public $password;

    /**
     *  @var int
     */
    public $role;

    public function __construct($mail, $password, $role, $id = NULL){
        $this->id = $id;
        $this->mail = $mail;
        $this->password = $password;
        $this->role = $role;
    }

    // Au cas où on met les attributs en private

/*     public function get_id(){
        return $this->id;
    }

    public function get_role(){
        return $this->role;
    }

    public function get_mail(){
        return $this->mail;
    }

    public function get_password(){
        return  $this->password;
    } */
}

