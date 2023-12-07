<?php
class apartmentModel{
    public $area; //In square meters
    public $capacity; //Number of people who can live in the apartment
    public $address;
    public $disponibilty; //False if occupied, True if free
    public $price;
    public $owner; //Name of the owner

    public function __construct($address, $area, $owner, $capacity, $price, $disponibilty){
        $this->area;
        $this->capacity;
        $this->address;
        $this->disponibilty;
        $this->price;
        $this->owner;
    }
}
?>