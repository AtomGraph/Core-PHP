<?php

namespace Graphity\Tests\Sparql;

use Graphity\Rdf as Rdf;
use Graphity\Sparql as Sparql;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    const QUERY = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
PREFIX dc: <http://purl.org/dc/elements/1.1/> 
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX dct: <http://purl.org/dc/terms/> 
PREFIX rev: <http://purl.org/stuff/rev#> 
PREFIX wm: <http://semantic-web.dk/ontologies/wulffmorgenthaler#> 
PREFIX hn: <http://semantic-web.dk/ontologies/heltnormalt#>
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

CONSTRUCT {
    ?post rdf:type sioc:Forum .
    ?post rdf:type hn:StripPost . 
    ?post rdf:type ?superType .
    ?post foaf:depiction ?image .
    ?image foaf:thumbnail ?thumb .
    ?post dct:issued ?date .
    ?post hn:rating ?rating .
    ?post dc:title ?title .
    ?post dct:created ?created .
    ?post dct:modified ?modified .
    ?post dc:subject ?keyword .
    ?post sioc:has_container ?container .
    ?keyword skos:prefLabel ?label .
}
WHERE {
    GRAPH ?ontology
    {
        ?post rdf:type sioc:Forum .
        hn:StripPost rdfs:subClassOf ?superType .
    }
    OPTIONAL
    {
        SELECT *
        WHERE {
            GRAPH ?postGraph
            {
                ?post rdf:type hn:StripPost . 
                ?post foaf:depiction ?image .
                ?image foaf:thumbnail ?thumb .
                ?post dct:issued ?date .
                ?post sioc:has_container ?container .
                OPTIONAL { ?post hn:rating ?rating }
                OPTIONAL { ?post dc:title ?title } 
                OPTIONAL { ?post dct:created ?created }
                OPTIONAL { ?post dct:modified ?modified }
                FILTER (?date <= ?today)
            }
        }
        ORDER BY ?orderDir(?orderBy)
        OFFSET ?offset
        LIMIT ?limit
    }
    OPTIONAL
    {
        ?post dc:subject ?keyword .
        ?keyword skos:prefLabel ?label
    }
}";

    const POST = "http://example.org/post/2011/01/01";
    const TODAY = "2011-10-28T17:11:11+03:00";
    const DT_TYPE = "http://www.w3.org/2001/XMLSchema#dateTime";
    const ORDERDIR = "DESC";
    const ORDERBY = "date";
    const OFFSET = 0;
    const LIMIT = 10;

    const EXPECTED_QUERY = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
PREFIX dc: <http://purl.org/dc/elements/1.1/> 
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX dct: <http://purl.org/dc/terms/> 
PREFIX rev: <http://purl.org/stuff/rev#> 
PREFIX wm: <http://semantic-web.dk/ontologies/wulffmorgenthaler#> 
PREFIX hn: <http://semantic-web.dk/ontologies/heltnormalt#>
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

CONSTRUCT {
    <http://example.org/post/2011/01/01> rdf:type sioc:Forum .
    <http://example.org/post/2011/01/01> rdf:type hn:StripPost . 
    <http://example.org/post/2011/01/01> rdf:type ?superType .
    <http://example.org/post/2011/01/01> foaf:depiction ?image .
    ?image foaf:thumbnail ?thumb .
    <http://example.org/post/2011/01/01> dct:issued ?date .
    <http://example.org/post/2011/01/01> hn:rating ?rating .
    <http://example.org/post/2011/01/01> dc:title ?title .
    <http://example.org/post/2011/01/01> dct:created ?created .
    <http://example.org/post/2011/01/01> dct:modified ?modified .
    <http://example.org/post/2011/01/01> dc:subject ?keyword .
    <http://example.org/post/2011/01/01> sioc:has_container ?container .
    ?keyword skos:prefLabel ?label .
}
WHERE {
    GRAPH ?ontology
    {
        <http://example.org/post/2011/01/01> rdf:type sioc:Forum .
        hn:StripPost rdfs:subClassOf ?superType .
    }
    OPTIONAL
    {
        SELECT *
        WHERE {
            GRAPH ?postGraph
            {
                <http://example.org/post/2011/01/01> rdf:type hn:StripPost . 
                <http://example.org/post/2011/01/01> foaf:depiction ?image .
                ?image foaf:thumbnail ?thumb .
                <http://example.org/post/2011/01/01> dct:issued ?date .
                <http://example.org/post/2011/01/01> sioc:has_container ?container .
                OPTIONAL { <http://example.org/post/2011/01/01> hn:rating ?rating }
                OPTIONAL { <http://example.org/post/2011/01/01> dc:title ?title } 
                OPTIONAL { <http://example.org/post/2011/01/01> dct:created ?created }
                OPTIONAL { <http://example.org/post/2011/01/01> dct:modified ?modified }
                FILTER (?date <= \"2011-10-28T17:11:11+03:00\"^^<http://www.w3.org/2001/XMLSchema#dateTime>)
            }
        }
        ORDER BY DESC(?date)
        OFFSET 0
        LIMIT 10
    }
    OPTIONAL
    {
        <http://example.org/post/2011/01/01> dc:subject ?keyword .
        ?keyword skos:prefLabel ?label
    }
}";

    public function test_parepareQuery() {
        $query = Sparql\Query::newInstance()
            ->setQuery(self::QUERY)
            ->setVariable('post', new Rdf\Resource(self::POST))
            ->setVariable('today', new Rdf\Literal(self::TODAY, self::DT_TYPE))
            ->setParameter('orderDir', new Sparql\Keyword(self::ORDERDIR))
            ->setParameter('orderBy', new Sparql\Variable(self::ORDERBY))
            ->setParameter('offset', new Sparql\Integer(self::OFFSET))
            ->setParameter('limit', new Sparql\Integer(self::LIMIT));

        $this->assertEquals(self::EXPECTED_QUERY, (string)$query);
    }
}


