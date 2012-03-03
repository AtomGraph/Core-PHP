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

namespace Graphity\Rdf;

/**
 * An RDF literal.
 * 
 * Based on Jena: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/Literal.html
 */
class Literal implements NodeInterface
{

    /**
     * @var string
     */
    protected $dataType = null;

    /**
     * @var string
     */
    protected $language = null;

    /**
     * @var string
     */
    protected $objectLiteral = null;

    
    /**
     * Empty constructor
     */
    public function __construct($value, $dataType = null, $language = null) {
        $this->setObjectLiteral($value);
        $this->setDatatype($dataType);
        $this->setLanguage($language);
    }
    
    /**
     * @param string $dataType
     * 
     * @return Literal
     */
    public function setDatatype($dataType)
    {
        $this->dataType = $dataType;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getDatatype()
    {
        return $this->dataType;
    }

    /**
     * @return boolean
     */
    public function hasDatatype()
    {
        return ($this->dataType !== null);
    }

    /**
     * @param string $language
     * 
     * @return Literal
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return boolean
     */
    public function hasLanguage()
    {
        return ($this->language !== null);
    }

    /**
     * @param string $value
     * 
     * @return Literal
     */
    public function setObjectLiteral($value)
    {
        $this->objectLiteral = $value;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectLiteral()
    {
        return $this->objectLiteral;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return ($this->isLiteral() ? $this->objectLiteral : $this->objectUri);
    }

    /* (non-PHPdoc)
     * @see Rdf\Node::isAnonymous()
     */
    public function isAnonymous()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see Rdf\Node::isLiteral()
     */
    public function isLiteral()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see Rdf\Node::isResource()
     */
    public function isResource()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see Rdf\Node::isURIResource()
     */
    public function isURIResource()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see Rdf\Node::__toString()
     */
    public function __toString()
    {
        $format = "\"%s\"";
        $object = "";
        if(strstr($this->getValue(), "\n") !== false || strstr($this->getValue(), "\r") !== false || strstr($this->getValue(), "\t") !== false) {
            $format = "\"\"\"%s\"\"\"";
            $object = sprintf($format, $this->getValue());
        } else {
            $object = sprintf($format, addcslashes($this->getValue(), "\""));
        }
        if($this->hasDatatype()) {
            $format = "^^%s";
            if(strpos($this->getDatatype(), "http://") === 0) {
                $format = "^^<%s>";
            }
            $object .= sprintf($format, $this->getDatatype());
        } else if($this->hasLanguage()) {
            $format = "@%s";
            $object .= sprintf($format, $this->getLanguage());
        }
        
        return $object;
    }

}
