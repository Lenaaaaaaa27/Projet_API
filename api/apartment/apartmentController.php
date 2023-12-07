<?php
class apartmentController{
    private $service;

    public __construct(){
        $this->service = new apartmentService();
    }
}
?>