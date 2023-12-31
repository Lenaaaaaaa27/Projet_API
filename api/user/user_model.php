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

    /**
     *  @var string
     */

    public $token;

    /**
     *  @var array
     */

    public $ownedApartments;

    /**
     *  @var array
     */

    public $madeReservations;

    public function __construct($mail, $password, $role, $id = NULL, $token = NULL, $ownedApartments = NULL, $madeReservations = NULL){
        $this->mail = $mail;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
        $this->token = $token;
    }
}

