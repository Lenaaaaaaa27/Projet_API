<?php 

class BDDException extends Exception {
    public function __construct($message = "An error occured with the database.") {
        parent::__construct($message);
    }
}

class BDDNotFoundException extends Exception {
    public function __construct($message = "Could not find object.") {
        parent::__construct($message);
    }
}
