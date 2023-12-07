<?php
class ApartmentController{
    private $service;

    public __construct(){
        $this->service = new apartmentService();
    }
}
?>