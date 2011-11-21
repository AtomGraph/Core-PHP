<?php

namespace Graphity\View;

use Graphity\Resource;
use Graphity\Response;
use Graphity\Util\URIResolver;
use Graphity\View;

class JSONLDView extends View
{
    public function __construct(Resource $resource)
    {
        parent::__construct($resource);
        $this->setCharacterEncoding("UTF-8");
        $this->setContentType(ContentType::APPLICATION_JSON);
        $this->setStatus(Response::SC_OK);
    }

    public function display()
    {
		$doc = new \DOMDocument();
		$doc->loadXML($this->getResource()->describe());

		$styleSheet = new \DOMDocument();
		$styleSheet->load(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "webapp" . DIRECTORY_SEPARATOR . "WEB-INF" . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "rdf2json-ld.xsl");

		$transformer = new \XSLTProcessor();
		$transformer->importStyleSheet($styleSheet);

        //$this->write();
        fwrite($this->getWriter(), $transformer->transformToXML($doc));
    }

}