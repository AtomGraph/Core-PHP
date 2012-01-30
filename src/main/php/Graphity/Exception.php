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

namespace Graphity;

use Graphity\Rdf;
use Graphity\Vocabulary as Vocabulary;

/**
 * This should be the root of all Graphity Exceptions
 */
class Exception extends \RuntimeException
{
    public function toModel()
    {
        $model = new Rdf\Model();

        $model->addStatement(new Rdf\Statement(new Rdf\Resource("_:exc"), new Rdf\Property(Vocabulary\Rdf::type), new Rdf\Resource(Vocabulary\Graphity::Exception)));
        $model->addStatement(new Rdf\Statement(new Rdf\Resource("_:exc"), new Rdf\Property(Vocabulary\Http::statusCodeNumber), new Rdf\Literal($this->getCode(), Vocabulary\XSD::int)));
        $model->addStatement(new Rdf\Statement(new Rdf\Resource("_:exc"), new Rdf\Property(Vocabulary\DC::description), new Rdf\Literal($this->getMessage())));
        $model->addStatement(new Rdf\Statement(new Rdf\Resource("_:exc"), new Rdf\Property(Vocabulary\Graphity::trace), new Rdf\Literal($this->getTraceAsString())));

        return $model;
    }

}
