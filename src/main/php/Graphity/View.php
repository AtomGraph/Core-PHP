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

/**
 * An abstract class for sub-classing by the actual view classes.
 */
abstract class View extends Response
{

    private $resource = null;

    /**
     * Constructs View from Resource.
     * @param Resource $resource Resource
     */
    
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->setStatus(Response::SC_OK);
        $this->setCharacterEncoding("UTF-8");
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getRequest()
    {
        return $this->getResource()->getRequest();
    }

}

