<?php

namespace Graphity;

class ResponseWrapper implements ResponseInterface
{

    private $response = null;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function addCookie(Cookie $cookie)
    {
        $this->response->addCookie($cookie);
    }

    public function setHeader($name, $value)
    {
        $this->response->setHeader($name, $value);
    }

    public function getStatus()
    {
        return $this->response->getStatus();
    }

    public function setStatus($status)
    {
        $this->response->setStatus($status);
    }

    public function getContentType()
    {
        return $this->response->getContentType();
    }

    public function setContentType($contentType)
    {
        $this->response->setContentType($contentType);
    }

    public function getCharacterEncoding()
    {
        return $this->response->getCharacterEncoding();
    }

    public function setCharacterEncoding($encoding)
    {
        $this->response->setCharacterEncoding($encoding);
    }

    public function sendRedirect($uri)
    {
        $this->response->sendRedirect($uri);
    }

    public function write($string)
    {
        $this->response->write($string);
    }

    public function getBuffer()
    {
        return $this->response->getBuffer();
    }
}

?>
