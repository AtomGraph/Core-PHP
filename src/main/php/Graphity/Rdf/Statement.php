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
 * An RDF statement.
 * 
 * Based on Jena: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/Statement.html
 */
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
                throw new \Graphity\Exception("Unknown field: '" . $offset . "'");
        }
    }
    
    public function offsetSet($offset, $value) {
        throw new \Graphity\Exception("Can not set statement values using array interface. Consider using Statement::get* methods.");
    }
    
    public function offsetUnset($offset) {
        throw new \Graphity\Exception("Can not unset statement values using array interface. Consider removing Statement from model.");
    }
}
