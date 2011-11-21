<?php

/**
 *  Copyright 2011 Graphity Team
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  @package        graphity
 *  @author         Martynas Jusevičius <pumba@xml.lt>
 *  @link           http://graphity.org/
 */

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

    public function getWriter()
    {
        return $this->response->getWriter();
    }
}

?>
