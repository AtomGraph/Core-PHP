<?php

namespace Graphity;

abstract class RequestWrapper implements RequestInterface
{

    private $request = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
