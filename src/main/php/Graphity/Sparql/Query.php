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

namespace Graphity\Sparql;

use Graphity\Rdf as Rdf;

/**
 *  A query abstraction.
 *
 *  Provided the query, variable and parameter maps - returns a new query
 *  with SPARQL variables changed to given parameters.
 *
 *  Notice that variables are mostly RDF data (resource uri, literal) while
 *  parameter is integer or _variable name_, used in 'ORDER BY', 'LIMIT', 
 *  'OFFSET' clauses of the query.
 */
class Query
{
    /**
     *  @var array
     */
    protected $parameterMap = array();

    /**
     *  @var array
     */
    protected $variableMap = array();

    /**
     *  @var string
     */
    protected $query = null;

    public function __construct($query = null) {
        $this->query = $query;
    }

    /**
     *  Set query
     *
     *  @param string $query
     *
     *  @return Query
     */
    public function setQuery($query) {
        $this->query = $query;

        return $this;
    }

    /**
     *  Return a new Query instance.
     *
     *  @return Query
     */
    public static function newInstance() {
        return new Query();
    }

    /**
     *  Set a variable value.
     *
     *  @param string $name
     *  @param Rdf\Node $var
     *
     *  @return Query
     */
    public function setVariable($name, Rdf\Node $var) {
        $this->variableMap[$name] = $var;

        return $this;
    }

    /**
     *  Set a parameter value.
     *
     *  @param string $name
     *  @param Parameter $param
     *
     *  @return Query
     */
    public function setParameter($name, ParameterAbstract $param) {
        $this->parameterMap[$name] = $param;

        return $this;
    }

    /**
     *  Get variable map.
     *
     *  @return array
     */
    public function getVariableMap() {
        return $this->variableMap;
    }

    /**
     *  Get parameter map.
     *
     *  @return array
     */
    public function getParameterMap() {
        return $this->parameterMap;
    }

    /**
     *  Return prepared query.
     *
     *  @return string
     */
    public function prepareQuery() {
        $preparedQuery = $this->query;

        /** prepare parameters */
        foreach($this->getParameterMap() as $name => $value) {
            $preparedQuery = preg_replace('/\?' . $name . '\b/i', (string)$value, $preparedQuery); 
        }

        /** prepare variables */
        foreach($this->getVariableMap() as $name => $value) {
            $preparedQuery = preg_replace('/\?' . $name . '\b/i', (string)$value, $preparedQuery);
        }

        return $preparedQuery;
    }

    /**
     *  Return string representation of Query instance.
     *
     *  @return string
     */
    public function __toString() {
        return $this->prepareQuery();
    }
}
