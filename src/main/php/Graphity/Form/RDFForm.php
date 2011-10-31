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

use Graphity;
use Graphity\Rdf\Model;
use Graphity\Rdf\Literal;
use Graphity\Rdf\Statement;
use Graphity\Rdf\Resource;

/**
 *  Implementation of http://www.lsrn.org/semweb/rdfpost.html
 */
class RDFForm extends Graphity\Form
{

    /**
     * @var Model
     */
    private $model = null;

    private $keys = array();

    private $values = array();

    public function __construct(Graphity\Request $request)
    {
        $this->setModel(new Model());
        $this->initParamMap();
        $this->initModel();
    }

    private function initParamMap()
    {
        $postBody = stream_get_contents($this->getInputStream());
        fclose($this->getInputStream());
        
        $params = explode("&", $postBody);
        
        foreach($params as $key => $param) {
            $pair = explode("=", $param);
            if(count($pair) > 1) {
                $this->addKey(urldecode($pair[0]));
                $this->addValue(urldecode($pair[1]));
            }
        }
    }

    protected function initModel()
    {
        $subject = null;
        $predicate = null;
        $object = null;
        $datatype = null;
        $language = null;

        foreach($this->keys as $i => $key) {
            // simplified version, only supports su/pu/ou/ol/ll/lt
            

            if($key == "su") {
                $predicate = $object = $datatype = $language = null;
                $subject = $this->values[$i];
            }
            if($key == "sb") {
                $predicate = $object = $datatype = $language = null;
                $subject = "_:" . $this->values[$i];
            }
            if($key == "pu") {
                $object = $datatype = $language = null;
                $predicate = $this->values[$i];
            }
            if($key == "ou") {
                $object = new Resource($this->values[$i]);
            }
            if($key == "ol") {
                $datatype = $language = null;
                $object = $this->values[$i];

                if(isset($this->keys[$i + 1]) && ($this->keys[$i + 1] == "lt" || $this->keys[$i + 1] == "ll")) {
                    continue; // do not add triple yet
                }
            }
            if($key == "lt" || $key == "ll") {
                if($key == "lt")
                    $datatype = $this->values[$i];
                if($key == "ll")
                    $language = $this->values[$i];
            }
            if($key == "ob") {
                $object = new Resource("_:" . $this->values[$i]);
            }
            
            if($subject != null && $predicate != null && $object != null) {
                if(is_string($object)) {
                    $object = new Literal($object, $datatype, $language);
                }
                $this->model->addStatement(new Statement(new Resource($subject), new Resource($predicate), $object));
                $object = null; // so we don't duplicate on empty values
            }
        }
    
    }

    protected function addKey($key)
    {
        $this->keys[] = $key;
    }

    protected function addValue($value)
    {
        $this->values[] = $value;
    }

    protected function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Return model with statements.
     * 
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function validate()
    {
        return array();
    }
}
