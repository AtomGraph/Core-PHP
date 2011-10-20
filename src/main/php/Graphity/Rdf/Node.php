<?php
/**
 * Interface covering RDF resources and literals.
 * 
 * Based on this: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/RDFNode.html
 * 
 * @author Julius Seporaitis <julius@seporaitis.net>
 */

namespace Graphity\Rdf;

interface Node
{
    /**
     * Answer true iff this RDFNode is an anonymous resource.
     * 
     * @return boolean
     */
    public function isAnonymous();
    
    /**
     * Answer true iff this RDFNode is a Literal
     * 
     * @return boolean
     */
    public function isLiteral();
    
    /**
     * Answer true iff this RDFNode is a URI resource or an anonymous resource (ie is not literal).
     * 
     * @return boolean
     */
    public function isResource();
    
    /**
     * Answer true iff this RDFNode is a named resource (ie not anonymous resource and not literal).
     * 
     * @return boolean
     */
    public function isURIResource();
    
    /**
     * Return a string representation of the node.
     * 
     * @return string
     */
    public function __toString();
}
