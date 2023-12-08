<?php 

class ServiceException extends Exception {

    public function __construct($message = "An error occured.") {
        parent::__construct($message);
    }
}

class EmailAlreadyExist extends ServiceException{
    public function __construct($message = "Can't add User because email is already used") {
        parent::__construct(message: $message);
    }
}
?>
