<?php 

class ServiceException extends Exception {

    public function __construct($message = "An error occured.") {
        parent::__construct($message);
    }
}

class FailConnexionAccount extends ServiceException{
    public function __construct($message = "Can't delete this User, wrong email or password"){
        parent::__construct(message :$message);
    }
}

//We sould discuss of replacing the two followings by AlreadyExistsException when over

class EmailAlreadyExist extends ServiceException{
    public function __construct($message = "Can't add User because email is already used") {
        parent::__construct(message: $message);
    }
}

class ApartmentAlreadyExists extends ServiceException{
    public function __construct($message = "Apartment Already exists"){
        parent::__construct(message: $message);
    }
}

class ValidationException extends ServiceException{
    public function __construct($message = "Wrong data provided") {
        parent::__construct(message: $message);
    }
}
?>
