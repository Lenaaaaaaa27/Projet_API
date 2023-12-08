<?php

class ReservationModel {
    public $id;
    public $start_date;
    public $end_date;
    public $price;
    public $renter;
    public $apartment;

   /**
    * @param $id
    * @param $start_date
    * @param $end_date
    * @param $price
    * @param $renter
    * @param $apartment
    */
    public
    function __construct($id = null, $start_date, $end_date, $price, $renter, $apartment) {
        

        $this->id = $id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->price = $price;
        $this->renter = $renter;
        $this->apartment = $apartment;
    }
}