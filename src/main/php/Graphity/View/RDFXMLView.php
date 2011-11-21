<?php

namespace Graphity\View;

use Graphity\Resource;
use Graphity\Response;
use Graphity\Util\URIResolver;
use Graphity\View;

class RDFXMLView extends View
{
    public function __construct(Resource $resource)
    {
        parent::__construct($resource);
        $this->setCharacterEncoding("UTF-8");
        $this->setContentType(ContentType::APPLICATION_RDF_XML);
        $this->setStatus(Response::SC_OK);
    }

    public function display()
    {
        fwrite($this->getWriter(), $this->getResource()->describe());
    }

}