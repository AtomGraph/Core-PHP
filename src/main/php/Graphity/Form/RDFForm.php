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

namespace Graphity\Form;

use Graphity;
use Graphity\Request;
use Graphity\Response;
use Graphity\RequestInterface;
use Graphity\MultipartRequest;
use Graphity\UploadedFile;
use Graphity\MultipartParser;
use Graphity\FormInterface;
use Graphity\Rdf\Model;
use Graphity\Rdf\Literal;
use Graphity\Rdf\Statement;
use Graphity\Rdf\Resource;
use Graphity\WebApplicationException;

/**
 *  Implementation of http://www.lsrn.org/semweb/rdfpost.html
 */
class RDFForm implements RequestInterface, FormInterface // TO-DO: extends MultipartRequest?
{
    /**
     * @var Model
     */
    private $model = null;

    private $keys = array();

    private $values = array();

    private $request = null;

    protected $parameters = array();

    protected $files = array();

    private $multipart = false;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->model = new Model();
        $this->initParamMap();
        $this->initModel();
    }

    private function initParamMap()
    {
        if (strpos($this->request->getContentType(), MultipartRequest::CONTENT_TYPE) === 0) // $this->request instanceof MultipartRequest
        {
            $this->multipart = true;
            $parser = new MultipartParser($this->request, MultipartRequest::DEFAULT_MAX_POST_SIZE);

            while (($part = $parser->readNextPart()) != null)
                if ($part->getName() != null)
                {
                    $this->addKey($part->getName());
                    if ($part->isParam())
                    {
                        $this->addValue($part->getValue());
                        
                        // then do the same as MultipartRequest does - in order to allow access via getParameter()
                        $existingValues = array();
                        if (isset($this->parameters[$part->getName()])) $existingValues = $this->parameters[$part->getName()];
                        else $this->parameters[$part->getName()] = $existingValues;
                        $existingValues[] = $part->getValue();
                        $this->parameters[$part->getName()] = $existingValues;
                    }
                    if ($part->isFile())
                    {
                        $this->addValue($part->getTmpName());

                        // then do the same as MultipartRequest does - in order to access files
                        if ($part->getFileName() != null)
                        {
                            $this->files[$part->getName()] = new UploadedFile(sys_get_temp_dir(), $part->getFileName(), $part->getFileName(), $part->getContentType()); // what about the original filename?
                            $part->writeTo(sys_get_temp_dir()); // save the file
                        }
                        else
                            $this->files[$part->getName()] = new UploadedFile(null, null, null, null);
                    }
                }
        }
        else
        {
            $postBody = stream_get_contents($this->request->getInputStream());
            fclose($this->request->getInputStream());
            
            $params = explode("&", $postBody);
            
            foreach($params as $key => $param) {
                $pair = explode("=", $param);
                if(count($pair) > 1) {
                    $this->addKey(urldecode($pair[0]));
                    $this->addValue(urldecode($pair[1]));

                    // then do the same as MultipartRequest does - in order to allow access via getParameter()
                    $existingValues = array();
                    if (isset($this->parameters[$pair[0]])) $existingValues = $this->parameters[$pair[0]];
                    else $this->parameters[$pair[0]] = $existingValues;
                    $existingValues[] = $pair[1];
                    $this->parameters[$pair[0]] = $existingValues;
                }
            }
        }
    }

    private function initModel()
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
        $errors = array();

        // check if we have the data
        if(count($this->model->getStatements()) === 0) {
            throw new WebApplicationException("Form data is missing", Response::SC_BAD_REQUEST);
        }

        return $errors;
    }

    /**
     * Return true if this form is multipart.
     *
     * @return boolean
     */
    public function isMultipart() {
        return $this->multipart;
    }

    public function getFile($name) {
        try {
            $file = $this->files[$name];
            return $file->getFile(); // may be null
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getParameter($name)
    {
        try
        {
            $values = null;
            if (isset($this->parameters[$name]))
                $values = $this->parameters[$name];
            if ($values == null || count($values) == 0)
                return null;

            $value = $values[count($values) - 1];
            return $value;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    public function getAttribute($name)
    {
        return $this->request->getAttribute($name);
    }

    public function setAttribute($name, $value)
    {
        $this->request->setAttribute($name, $value);
    }

    public function getContentType()
    {
        return $this->request->getContentType();
    }

    public function getContentLength()
    {
        return $this->request->getContentLength();
    }

    public function getCookies()
    {
        return $this->request->getCookies();
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function getHeader($name)
    {
        return $this->request->getHeader($name);
    }

    public function getSession()
    {
        return $this->request->getSession();
    }

    public function getParameterMap()
    {
        return $this->request->getParameterMap();
    }

    public function getPathInfo()
    {
        return $this->request->getPathInfo();
    }

    public function getRequestURI()
    {
        return $this->request->getRequestURI();
    }

    public function getServerName()
    {
        return $this->request->getServerName();
    }

    public function getServerPort()
    {
        return $this->request->getServerPort();
    }

    public function getScheme()
    {
        return $this->request->getScheme();
    }

    public function getQueryString()
    {
        return $this->request->getQueryString();
    }

	public function getInputStream()
    {
        return $this->request->getInputStream();
    }

}
