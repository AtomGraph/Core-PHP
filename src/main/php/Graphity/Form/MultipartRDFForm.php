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
