<?php
/**
 * An RDF statement.
 * 
 * Based on this: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/Statement.html
 * 
 * @author Julius Seporaitis <julius@seporaitis.net>
 */

namespace Graphity\Rdf;

class Statement implements \ArrayAccess
{
    /**
     * @var Resource
     */
    protected $subject;
    
    /**
     * @var Resource
     */
    protected $predicate;
    
    /**
     * @var Node
     */
    protected $object;
    
    /**
     * Create Statement
     * 
     * @param string $subject
     * @param string $predicate
     * @param string|Literal $object
     */
    public function __construct(Resource $subject, Resource $predicate, Node $object) {
        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;
    }
    
    /**
     * @param string $subject
     * 
     * @return Statement
     */
    public function setSubject(Resource $subject) {
        $this->subject = $subject;
        
        return $this;
    }
    
    /**
     * @return Resource
     */
    public function getSubject() {
        return $this->subject;
    }
    
    /**
     * @param Resource $predicate
     * 
     * @return Statement
     */
    public function setPredicate(Resource $predicate) {
        $this->predicate = $predicate;
        
        return $this;
    }
    
    /**
     * @return Resource
     */
    public function getPredicate() {
        return $this->predicate;
    }
    
    /**
     * @param Node $object
     * 
     * @return Statement
     */
    public function setObject(Node $object) {
        $this->object = $object;
        
        return $this;
    }
    
    /**
     * @return Node
     */
    public function getObject() {
        return $this->object;
    }
    
    /**
     * @return string
     */
    public function __toString() {
        return sprintf("%s %s %s .\n", (string)$this->getSubject(), (string)$this->getPredicate(), (string)$this->getObject());
    }
    
    public function offsetExists($offset) {
        $objectKey = 'objectLiteral';
        if($this->object->isResource()) {
            $objectKey = 'objectUri';
        }
        
        $listOfOffsets = array(
            'subject',
            'predicate',
            $objectKey,
        );
        
        if($this->object->isLiteral()) {
            if($this->object->hasDatatype()) {
                $listOfOffsets[] = 'type';
            }
            
            if($this->object->hasLanguage()) {
                $listOfOffsets[] = 'lang';
            }
        }
        
        return in_array($offset, $listOfOffsets);
    }
    
    public function offsetGet($offset) {
        switch($offset) {
            case 'subject':
                return $this->getSubject();
            case 'predicate':
                return $this->getPredicate();
            case 'objectLiteral':
                return ($this->object->isLiteral()) ? $this->object : null;
            case 'objectUri':
                return ($this->object->isResource()) ? $this->object : null;
            case 'type':
                return ($this->object->isLiteral()) ? $this->object->getDatatype() : null;
            case 'lang':
                return ($this->object->isLiteral()) ? $this->object->getLanguage() : null;
            default:
                throw new RuntimeException("Unknown field: '" . $offset . "'");
        }
    }
    
    public function offsetSet($offset, $value) {
        throw new RuntimeException("Can not set statement values using array interface. Consider using Statement::get* methods.");
    }
    
    public function offsetUnset($offset) {
        throw new RuntimeException("Can not unset statement values using array interface. Consider removing Statement from model.");
    }
}
