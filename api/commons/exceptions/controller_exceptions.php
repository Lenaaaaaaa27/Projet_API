<?php 

class HTTPException extends Exception {

    public function __construct($message = "An error occured.", $code = 500) {
        parent::__construct($message, $code);
    }
}


class NotFoundException extends HTTPException {
    public function __construct($message = "Not Found") {
        parent::__construct(message: $message, code: 404);
    }
}

class BadRequestException extends HTTPException {
    public function __construct($message = "Bad Request") {
        parent::__construct(message: $message, code: 400);
    }
}
?>
