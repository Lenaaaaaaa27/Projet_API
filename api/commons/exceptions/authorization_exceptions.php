<?php

class AuthorizationException extends Exception{
    public function __construct($message = "An error occured.", $code = 401) {
        parent::__construct($message, $code);
    }
}

class AccessException extends Exception{
    public function __construct($message = "An error occured.", $code = 403) {
        parent::__construct($message, $code);
    }
}

class ExpiredTokenException extends AuthorizationException{
    public function __construct($message = "Token is expirated !"){
        parent::__construct(message :$message);
    }
}

class NoToken extends AuthorizationException{
    public function __construct($message = "Token is NULL !"){
        parent::__construct(message :$message);
    }
}

class TokenDoesntExistException extends AuthorizationException{
    public function __construct($message = "Token doesn't exist !"){
        parent::__construct(message :$message);
    }
}

class AdminAccessException extends AccessException{
    public function __construct($message = "An error occured.", $code = 403) {
        parent::__construct($message, $code);
    }
}

class OwnerAccessException extends AccessException{
    public function __construct($message = "An error occured.", $code = 403) {
        parent::__construct($message, $code);
    }
}