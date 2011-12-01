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

namespace Graphity\Util;

use Graphity\Rdf\Model;
use Graphity\Rdf\Statement;
use Graphity\Rdf\Resource;
use Graphity\Rdf\Property;
use Graphity\Rdf\Literal;
use Graphity\Vocabulary\Rdf;
use Graphity\Exception;

/**
 * Class converting DOMDocument with RDF/XML into Graphity\Rdf\Model.
 * 
 * Based on Jena: http://openjena.org/javadoc/com/hp/hpl/jena/rdf/arp/DOM2Model.html
 */

class DOM2Model
{
    // TO-DO: make it work with default namespace + base URI
    // TO-DO: make it work recursively with hierarchical RDF/XML
    public static function transform(\DOMDocument $doc, $base)
    {
        if ($doc == null || $doc->documentElement->namespaceURI != Rdf::NS || $doc->documentElement->localName != "RDF")
            throw new Exception("Not a RDF/XML document");

        $model = new Model;

        if ($doc->documentElement != null && $doc->documentElement->hasChildNodes()) {
            foreach ($doc->documentElement->childNodes as $subjectElem) {
                if ($subjectElem->nodeType == XML_ELEMENT_NODE /* && $subjectElem->namespaceURI != null && $subjectElem->namespaceURI == Rdf::NS*/) {
                    // subject
                    $subject = $subjectId = null;
                    if ($subjectElem->getAttributeNS(Rdf::NS, "nodeID") != null) {
                        $subjectId = "_:" . $subjectElem->getAttributeNS(Rdf::NS, "nodeID");
                    }
                    //$subjectId = $subjectElem->getAttributeNS(Rdf::NS, "ID");
                    if ($subjectElem->getAttributeNS(Rdf::NS, "about") != null) {
                        $subjectId = $subjectElem->getAttributeNS(Rdf::NS, "about");
                    }
                    $subject = new Resource($subjectId);

                    foreach ($subjectElem->childNodes as $propertyElem) {
                        if ($propertyElem->nodeType == XML_ELEMENT_NODE) {
                            // property
                            $property = $propertyUri = null;
                            if ($propertyElem->namespaceURI != null && $propertyElem->localName != null) {
                                $propertyUri = $propertyElem->namespaceURI . $propertyElem->localName;
                            }
                            if ($propertyUri != null) {
                                $property = new Property($propertyUri);
                            }
                            else {
                                throw new Exception("Could not resolve property URI");
                            }
                           
                            // object
                            $object = $objectId = null;
                            if ($propertyElem->getAttributeNS(Rdf::NS, "nodeID") != null) {
                                $objectId = "_:" . $propertyElem->getAttributeNS(Rdf::NS, "nodeID");
                            }
                            if ($propertyElem->getAttributeNS(Rdf::NS, "resource") != null) {
                                $objectId = $propertyElem->getAttributeNS(Rdf::NS, "resource");
                            }
                            if (strlen($propertyElem->nodeValue) == 0) {
                                $object = new Resource($objectId);      
                            }
                            else 
                            {
                                $object = new Literal($propertyElem->nodeValue, $propertyElem->getAttributeNS(Rdf::NS, "datatype"), $propertyElem->getAttributeNS("http://www.w3.org/XML/1998/namespace", "lang"));
                            }

                            // add rdf:type if $subjectElem is not rdf:Description
                            $model->addStatement(new Statement($subject, $property, $object));
                        }
                    }
                }
            }
        }

        return $model;
    }
}
