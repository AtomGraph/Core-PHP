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
 *  @author         Julius Šėporaitis <julius@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity\Repository;

use Graphity\Rdf\Model;
use Graphity\Sparql\Query;
use Graphity\View\ContentType;

/**
 * Interface to the data repository.
 */
interface RepositoryInterface
{
    /**
     * Insert an Rdf\Model serialization into the Repository.
     *
     * You can also specify an optional graph URI.
     *
     * @param Rdf\Model $model  Model instance
     * @param string $graph     Optional graph URI.
     *
     * @return boolean
     */
    public function insert(Model $model, $graph = null);

    /**
     * Query the Repository with a SPARQL query.
     *
     * Should try to retrieve RDF/XML and return values grouped by
     * resource.
     *
     * If $accept is "application/sparql-xml" it will return raw xml
     * as received from the datastore.
     *
     * @param Sparql\Query $query   Query instance
     * @param stirng $accept        Response type (default: "application/rdf-xml")
     *
     * @throws Graphity\Sparql\Repository\Exception in case of error.
     *
     * @return string
     */
    public function query(Query $query, $accept = ContentType::APPLICATION_RDF_XML);

    /**
     * Update data in the repository with a SPARQL query.
     *
     * @param Sparql\Query $query   Query instance
     *
     * @throws Graphity\Sparql\Repository\Exception in case of error.
     *
     * @return string
     */
    public function update(Query $query);

    /**
     * Execute SPARQL ASK query.
     *
     * @param string $query
     *
     * @return boolean
     */
    public function ask(Query $query);
}

