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

interface ResourceInterface
{
    /**
     *  @see vhost.conf
     *
     *  This is required for multipart/form-data request parsing.
     */
    const MULTIPART_FORM = "multipart/form-data-alternate"; // comes from vhost.conf

    /**
     *  Return current resource URI.
     *
     *  @return string
     */
    function getURI();

    /**
     *  Get the path of the current request relative to the base URI as a string
     *
     *  @return string
     */
    function getPath();

    /**
     * Check if resource exists.
     * 
     * @return boolean
     */
    function exists();

    /**
     * Check if agent has access to resource.
     * 
     * @return boolean
     */
    function authorize();

    /**
     * Returns description of the resource (usually as RDF/XML).
     * 
     * @return DOMDocument
     */
    function describe();

}

