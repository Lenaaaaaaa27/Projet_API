<?php
class apartmentService{
    private $repository;

    public __construct(){
        $this->repository = new apartmentRepository();
    }
}
?>