<?php

/* Implementation of http://www.lsrn.org/semweb/rdfpost.html */

namespace Graphity\Form;

use Graphity;
use Graphity\Rdf\Model;
use Graphity\Rdf\Literal;
use Graphity\Rdf\Statement;
use Graphity\Rdf\Resource;

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
        $this->initParamMap($request);
        $this->initModel();
    }

    private function initParamMap(Graphity\Request $request)
    {
        $postBody = stream_get_contents($request->getInputStream());
        fclose($request->getInputStream());
        
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
