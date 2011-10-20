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
 *  Abstract class for sub-classing. Used to represent data submitted from a HTML form and access the Request parameters in a convenient way.
 */
abstract class Form
{

    //protected $request = null;
    

    /**
     * Constructs Form from Request.
     */
    
    public abstract function __construct(Request $request);

    /**
     * Validates this form and returns array of errors.
     * @return array An array of Errors
     */
    
    public abstract function validate();

    /**
     * Return true if this form is multipart.
     *
     * @return boolean
     */
    public function isMultipart() {
        return false;
    }

}

