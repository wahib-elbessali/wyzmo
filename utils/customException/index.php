<?php

class CustomException extends Exception
{
    public $statusCode;
    public $message;
    public $data;
    public $ok;
    public function __construct($statuscode, $message, $data, $ok)
    {
        $this->statusCode = $statuscode;
        $this->message = $message;
        $this->data = $data;
        $this->ok = $ok;
    }

}
