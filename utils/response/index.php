<?php

class Response
{
    public $message;
    public $statusCode;
    public $data;


    public function __construct($statusCode, $message, $data)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->data = $data;
    }
}
