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

use Graphity\Rdf;
use Graphity\Vocabulary as Vocabulary;

/**
 *  More information:
 *      - http://jsr311.java.net/nonav/javadoc/javax/ws/rs/WebApplicationException.html
 *      - http://php.net/manual/en/language.exceptions.extending.php
 */
class WebApplicationException extends Exception
{

    public function toModel()
    {
        $model = parent::toModel();

        $model->addStatement(new Rdf\Statement(new Rdf\Resource("_:exc"), new Rdf\Property(Vocabulary\Rdf::type), new Rdf\Resource(Vocabulary\Graphity::WebApplicationException)));

        return $model;
    }

}
