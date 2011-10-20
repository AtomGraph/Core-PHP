<?php

namespace Graphity\Form;

use Graphity\Rdf as Rdf;
use Graphity\MultipartParser;

class MultipartRDFForm extends RDFForm
{
    private $dir = null;

    public function __construct(MultipartParser $parser, $dir)
    {
        $this->dir = $dir;
        $this->setModel(new Rdf\Model());
        $this->initParamMap($parser);
        $this->initModel();
    }

    protected function initParamMap(MultipartParser $parser)
    {
        while (($part = $parser->readNextPart()) != null)
            if ($part->getName() != null)
            {
                $this->addKey($part->getName());
                if ($part->isParam()) $this->addValue($part->getValue());
                if ($part->isFile())
                {
                    $this->addValue($part->getTmpName());
                    $part->writeTo($this->dir); // TO-DO: writing files doesn't really belong here
                }
            }
    }

    public function isMultipart()
    {
        return true;
    }
}
