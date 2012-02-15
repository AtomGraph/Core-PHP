Requirements
============

PHP 5.3 or later. Might work on earlier PHP 5.x versions.
Java version is in the works.

Description
===========

JAX-RS
------

Supports JAX-RS-style RESTful API:

* resource annotations like `@Path` and `@GET`
* `UriBuilder` for building URIs out of components
* `ResponseBuilder` for building `Response` objects

Further implementation of missing JAX-RS features is planned.

More on JAX-RS: https://wikis.oracle.com/display/Jersey/Overview+of+JAX-RS+1.0+Features

RDF API
-------

Supports Jena-style object-oriented RDF API:

* `Model`
    * RDF/XML (DOM) serialization
    * Turtle serialization
* `Statement`
* `Resource`
* `Literal`

More on Apache Jena: http://incubator.apache.org/jena/

Utilities
---------

Includes utility classes for dealing with SPARQL, RDF/XML, and XSLT:

* `Repository` for remote SPARQL 1.1 endpoint access
* `QueryBuilder` for building SPARQL queries
* `XSLTBuilder` for building XSLT transformations
* `DOM2Model` for converting RDF/XML to Model (reverse of `Model::toDOM()`)

Usage
=====

To create a Graphity PHP application:

1.  Checkout or extract graphity-core into `/lib/graphity-core` or similar folder in your project.
    We recommend choosing the latest version tag on GitHub.

    Note: we also strongly recommend Maven directory structure, as it will be easier to share reusable resources with the Java version in the future. More on Maven Standard Directory Layout:
http://maven.apache.org/guides/introduction/introduction-to-the-standard-directory-layout.html

2.  Create some Resource class that imports and extends Graphity\Resource, for example:

        namespace My;

        use Graphity\Response;
        use Graphity\ResponseBuilder;
        use Graphity\View\ContentType;

        class Resource extends \Graphity\Resource

    Note: we strongly recommend using PHP namespaces with the standard folder layout (in Maven structure, that would be within the `src/main/php` folder). More ont the standard autoloader layout:
    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md

3.  Annotate the class with `@Path` and methods with `@GET`/`@POST` etc., for example:

        namespace My;

        use Graphity\Response;
        use Graphity\ResponseBuilder;
        use Graphity\View\ContentType;

        /** 
         * @Path("/{path}")
         */
        class Resource extends \Graphity\Resource
        {

            /**
             * @GET
             * @Produces("text/html")
             */
            public function getResponse()
            {
                return ResponseBuilder::newInstance()->
                    entity("<html>Hello world!</html>")->
                    status(Response::SC_OK)->
                    type(ContentType::TEXT_HTML)->
                    build();
            }
        }

    Note: PHP annotations must be embedded in `/* */` comment blocks.

    Note: `@Produces`/`@Consumes` annotations are not yet fully supported, but we recommend adding them for future compatibility.

4.  Run `/lib/graphity-core/bin/route_mapper.php` from the root folder of your namespace, specifying the location of your route file, for example (paths are relative to project root in this case):

        $ php lib/graphity-core/bin/route_mapper.php src/main/php/My src/main/php/routes.php

    This should generate a route file, which is used internally by Graphity to match request URIs against JAX-RS annotations.
    Note: this does not happen dynamically (as of yet), you have to re-map routes with `route_mapper.php` every time your annotations change.

5. Implement a subclass of `Graphity\Application`:

Documentation
=============

We need to do some work on this... Check out our [issues](https://github.com/Graphity/graphity-core/issues) so far.

Papers & presentations
----------------------

W3C "Linked Enterprise Data Patterns" workshop http://www.w3.org/2011/09/LinkedData/

* Graphity position paper http://www.w3.org/2011/09/LinkedData/ledp2011_submission_1.pdf
* Graphity presentation http://semantic-web.dk/presentations/LEDP2011.pdf

Libraries
=========

Graphity PHP uses following 3rd party libraries:

1.  Addendum (for annotation parsing)
    http://code.google.com/p/addendum/