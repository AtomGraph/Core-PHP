<?php
/**
 * An RDF resource.
 * 
 * Based on this: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/RDFResource.html
 * 
 * @author Julius Seporaitis <julius@seporaitis.net>
 */

namespace Graphity\Rdf;

class Resource implements Node
{

    /**
     * @var string Absolute resource URI or anonymous node ID (starts with "_:")
     */
    protected $uri = null;
    
    /**
     * Create a resource.
     * 
     * Set $uri to null for a blank node (bnode).
     * 
     * @param string $uri
     */
    public function __construct($uri = null) {
        if($uri == null) {
            $uri = "_:" . uniqid();
        }
        $this->uri = $uri;
    }

    /**
     * Return URI of the resource, or null if it's a bnode
     * 
     * @return string
     */
    public function getURI()
    {
        if($this->isAnonymous()) {
            return null;
        }
        return $this->uri;
    }
    
    /**
     * Return true iff this RDFResource is a URI resource with the given URI.
     * 
     * @return boolean
     */
    public function hasURI($uri)
    {
        return $this->isURIResource() && ($this->uri == $uri);
    }
    
    /**
     * Return anonymous id or null if it's not a bnode
     * 
     * @return string
     */
    public function getAnonymousId() {
        if($this->isAnonymous()) {
            return substr($this->uri, strlen("_:"));
        }
        return null;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isAnonymous()
     */
    public function isAnonymous()
    {
        return strpos($this->uri, "_:") === 0;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isLiteral()
     */
    public function isLiteral()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isResource()
     */
    public function isResource()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isURIResource()
     */
    public function isURIResource()
    {
        return ($this->isResource() && !$this->isAnonymous());
    }

    /* (non-PHPdoc)
     * @see RDFNode::__toString()
     */
    public function __toString()
    {
        return $this->isAnonymous() ? $this->uri : sprintf("<%s>", $this->uri);
    }

}
