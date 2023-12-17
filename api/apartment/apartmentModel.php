<?php
class ApartmentModel{
    public $id;
    public $area; //In square meters
    public $capacity; //Number of people who can live in the apartment
    public $address;
    public $disponibility; //False if occupied, True if free
    public $price;
    public $owner; //Name of the owner
    public $linkedReservations;

    public function __construct($id, $address, $area, $owner, $capacity, $price, $disponibility, $linkedReservations = NULL){
        $this->id = $id;
        $this->area = $area;
        $this->capacity = $capacity;
        $this->address = $address;
        $this->disponibility = $disponibility;
        $this->price = $price;
        $this->owner = $owner;
    }
}
?>