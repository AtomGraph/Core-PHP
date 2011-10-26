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

namespace Graphity\View;

/**
 * Serializes objects into XML strings.
 * It is an abstract class for sub-classing. An application Serializer should extend the framework Serializer and call the parent::serialize().
 */
abstract class Serializer
{
	public static function createDOMModel(Model $model, \DOMNode $parent)
	{
        if ($parent instanceof \DOMDocument) $doc = $parent;
        else $doc = $parent->ownerDocument;

		return $parent->appendChild($doc->createElementNS(Rdf::NS, "rdf:RDF"));
    }

	protected static function createDOMModelChildren(Model $model, \DOMElement $rdf)
	{
        foreach ($model->getStatements() as $stmt)
            self::createDOMStmt($stmt, $rdf);
    }

    protected static function createDOMStmt(Statement $stmt, \DOMElement $rdf)
    {
        //return $rdf->appendChild(
            self::createDOMObject($stmt->getObject(),
                self::createDOMPredicate($stmt->getPredicate(),
                    self::createDOMSubject($stmt->getSubject(), $rdf)));
    }

    protected static function createDOMSubject(Graphity\Rdf\Resource $subject, \DOMElement $stmt)
    {
        $descElem = $stmt->ownerDocument->createElementNS(Rdf::NS, "rdf:Description");
        if ($subject->isAnonymous()) $descElem->setAttributeNS(Rdf::NS, "rdf:nodeID", $subject->getAnonymousId());
        else $descElem->setAttributeNS(Rdf::NS, "rdf:about", $subject->getURI());
        return $stmt->appendChild($descElem);
    }

    protected static function createDOMPredicate(Graphity\Rdf\Resource $predicate, \DOMElement $subject)
    {
        $nsUri = $localName = null;
        // try predicate local name as substring after #
        if (strrpos($predicate->getURI(), "#") !== false)
        {
            $nsUri = substr($predicate->getURI(), 0, strrpos($predicate->getURI(), "#") + 1);
            $localName = substr($predicate->getURI(), strrpos($predicate->getURI(), "#") + 1);
        }
        // as a 2nd option try predicate local name as substring after /
        if ($nsUri === null && $localName === null && strrpos($predicate->getURI(), "/") !== false)
        {
            $nsUri = substr($predicate->getURI(), 0, strrpos($predicate->getURI(), "/") + 1);
            $localName = substr($predicate->getURI(), strrpos($predicate->getURI(), "/") + 1);
        }
        return $subject->appendChild($subject->ownerDocument->createElementNS($nsUri, "tmp" . ":" . $localName)); // uniqid() didn't work
    }

    protected static function createDOMObject(Graphity\Rdf\Node $object, \DOMElement $predicate)
    {
        if ($object->isLiteral())
        {
            if ($object->hasDatatype()) $predicate->setAttributeNS(Rdf::NS, "rdf:datatype", $object->getDatatype());
            return $predicate->appendChild($predicate->ownerDocument->createTextNode($object->getValue()));
        }
        if ($object->isResource())
        {
            if ($object->isAnonymous()) $predicate->setAttributeNS(Rdf::NS, "rdf:nodeID", $object->getAnonymousId());
            if ($object->isURIResource()) $predicate->setAttributeNS(Rdf::NS, "rdf:resource", $object->getURI());
        }
    }

}

