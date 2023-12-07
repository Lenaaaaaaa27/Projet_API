<?php
class ApartmentService{
    private $repository;

    public __construct(){
        $this->repository = new apartmentRepository();
    }
}
?>