<?php
/**
 * An RDF literal.
 * 
 * Based on this: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/Literal.html
 * 
 * @author Julius Seporaitis <julius@seporaitis.net>
 */

namespace Graphity\Rdf;

class Literal implements Node
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
     * @see RDFNode::isAnonymous()
     */
    public function isAnonymous()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isLiteral()
     */
    public function isLiteral()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isResource()
     */
    public function isResource()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isURIResource()
     */
    public function isURIResource()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see RDFNode::__toString()
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
