<?php
class ApartmentController{
    private $service;

    public function __construct(){
        $this->service = new ApartmentService();
    }
}
?>