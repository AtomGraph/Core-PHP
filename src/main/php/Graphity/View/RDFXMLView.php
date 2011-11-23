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

        fwrite($this->getWriter(), $this->getResource()->describe()->saveXML());
    }

}