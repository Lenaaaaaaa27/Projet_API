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

class EmailAlreadyExists extends ValueTakenException{
    public function __construct($message = "Can't add User because email is already used") {
        parent::__construct(message: $message);
    }
}

class ValueTakenException extends ServiceException {
    public function __construct($message = "Can't create object: one of its value is taken already.") {
        parent::__construct(message: $message);
    }
}
class EntityNotFoundException extends ServiceException {
    public function __construct($message = "Not Found") {
        parent::__construct(message: $message);
    }
}
class ValidationException extends ServiceException {
    public function __construct($message = "Wrong data provided!") {
        parent::__construct(message: $message);
    }
}
?>