<?php
/**
 * An RDF Model.
 * 
 * Based on this: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/Model.html
 * 
 * @author Julius Seporaitis <julius@seporaitis.net>
 */

namespace Graphity\Rdf;

class Model implements \Iterator, \ArrayAccess
{
    /**
     * @var array
     */
    protected $listOfStatements = array();
    
    /**
     * @var integer
     */
    protected $it = 0;
    
    /**
     * Add all the statements in the array to the model
     * 
     * @param array $statements
     * 
     * @return Model
     */
    public function addArray(array $listOfStatements) {
        $this->listOfStatements = array_merge($this->listOfStatements, $listOfStatements);
        
        return $this;
    }
    
    /**
     * Add a statement to the model
     * 
     * @param Statement $statement
     * 
     * return Model
     */
    public function addStatement(Statement $statement) {
        $this->listOfStatements[] = $statement;
        
        return $this;
    }

    /**
     * Return true if at least one Statement in this Model has $subject and $predicate
     *
     * @param RDFResource $subject
     * @param RDFResource $predicate
     *
     * @return boolean
     */
    public function contains(Resource $subject, Resource $predicate) {
        foreach($this->listOfStatements as $stmt) {
            if($stmt->getSubject()->getURI() === $subject->getURI() &&
                $stmt->getPredicate()->getURI() === $predicate->getURI()) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Return true if this Model has at least one identical statement.
     *
     * @param Statement $stmt
     * 
     * @return boolean
     */
    public function containsStatement(Statement $stmt) {
        foreach($this->listOfStatements as $s) {
            if((string)$s === (string)$stmt) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if this Model has at least one Statement which has the resource
     *
     * @param RDFNode $resource
     *
     * @return boolean
     */
    public function containsResource(Node $resource) {
       foreach($this->listOfStatements as $stmt) {
          if((string)$stmt->getSubject() === (string)$resource ||
              (string)$stmt->getPredicate() === (string)$resource ||
              (string)$stmt->getObject() === (string)$resource) {
              return true;
          }
       }

       return false;
    }

    /**
     * Answer a statement (s, p, ?O) from this Model.
     *
     * @param RDFResource $s
     * @param RDFResource $p
     *
     * @return Statement
     */
    public function getProperty(Resource $s, Resource $p) {
        foreach($this->listOfStatements as $stmt) {
            if($stmt->getSubject()->getURI() === $s->getURI() &&
                $stmt->getPredicate()->getURI() === $p->getURI()) {
                return $stmt;
            }
        }

        return null;
    }

    /**
     * Answer an iterator over all the resources in 
     * this model that have property p with value o.
     *
     * @param RDFResource $p
     * @param RDFNode $o
     *
     * @return array
     */
    public function listResourcesWithProperty(Resource $p, Node $o) {
        $list = array();
        foreach($this->listOfStatements as $stmt) {
            if((string)$stmt->getPredicate() === (string)$p &&
                (string)$stmt->getObject() === (string)$o) {
                $list[] = $stmt;
            }
        }

        return $list;
    }
    
    /**
     * Return array with statements
     * 
     * @return array
     */
    public function getStatements() {
        return $this->listOfStatements;
    }
    
    /**
     * Remove all statements in array from the model.
     * 
     * @param array $listOfStatements
     * 
     * @return Model
     */
    public function removeArray(array $listOfStatements) {
        foreach($this->listOfStatements as $idx => $stmt) {
            foreach($listOfStatements as $toDelete) {
                if($stmt->getSubject() == $toDelete->getSubject() &&
                   $stmt->getPredicate() == $toDelete->getPredicate() &&
                   $stmt->getObject() == $toDelete->getObject()) {
                    unset($this->listOfStatements[$idx]);
                    break;
                }
            }
        }
        $this->listOfStatements = array_values($this->listOfStatements);
        
        return $this;
    }
    
    /**
     * Remove statement from the model.
     * 
     * @param Statement $toDelete
     * 
     * @return Model
     */
    public function removeStatement(Statement $toDelete) {
        foreach($this->listOfStatements as $idx => $stmt) {
            if($stmt->getSubject() == $toDelete->getSubject() &&
               $stmt->getPredicate() == $toDelete->getPredicate() &&
               $stmt->getObject() == $toDelete->getObject()) {
                unset($this->listOfStatements[$idx]);
                break;
            }
        }
        
        $this->listOfStatements = array_values($this->listOfStatements);
        
        return $this;
    }
    
    /**
     * Remove all statements from this model.
     * 
     * @return Model
     */
    public function removeAll() {
        $this->listOfStatements = array();
        
        return $this;
    }
    
    /**
     * Magically return string representation of this model.
     * 
     * @return string
     */
    public function __toString() {
        $data = "";
        foreach($this->getStatements() as $stmt) {
            $data .= (string)$stmt;
        }
        
        return $data;
    }
    
	/* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->listOfStatements[$this->it];
    }

	/* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        $this->it++;
    }

	/* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->it;
    }

	/* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        return ($this->it >= 0 && ($this->it < count($this->listOfStatements)));
    }

	/* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->it = 0;
    }
    
	/* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->listOfStatements);
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset) ? $this->listOfStatements[$offset] : null);
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException("Consider using addArray/addStatement methods instead.");
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException("Consider using removeStatement/removeArray/removeAll methods instead.");
    }
}
