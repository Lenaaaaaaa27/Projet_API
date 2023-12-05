<?php 


class UserModel{
    /**
     *  @var int
     */
    public $id;

    /**
     *  @var int
     */
        public $role;

    public function __construct($id = NULL, $role){
        $this->id = $id;
        $this->role = $role;
    }

    // Au cas où on met les attributs en private

    /* public function get_id($id){
        return $this->id;
    }

    public function get_role($role){
        return $this->role;
    } */
}

