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
 *  @author         Martynas Jusevičius <martynas@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity\View;

use Graphity\Resource;
use Graphity\Response;
use Graphity\Util\XSLTBuilder;

class JSONLDView extends Response
{
    private $resource = null;

    public function __construct(Resource $resource)
    {
        parent::__construct();
        $this->resource = $resource;
        $this->setCharacterEncoding("UTF-8");
        $this->setContentType(ContentType::APPLICATION_JSON);
        $this->setStatus(Response::SC_OK);
    }

    public function flushBuffer()
    {
        fwrite($this->getWriter(), XSLTBuilder::fromStylesheetURI(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "webapp" . DIRECTORY_SEPARATOR . "WEB-INF" . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "rdf2json-ld.xsl")->
            document($this->resource->describe())->
            buildXML());
    }

}