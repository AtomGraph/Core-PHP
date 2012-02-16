Requirements
============

PHP 5.3 or later. Might work on earlier PHP 5.x versions.
Java version is in the works.

Description
===========

Graphity is a fully object-oriented PHP framework for building flexible [REST](http://en.wikipedia.org/wiki/REST)ful, [Semantic Web](http://en.wikipedia.org/wiki/Semantic_web), and/or [Linked Data](http://en.wikipedia.org/wiki/Linked_data) web applications.

Graphity tries *not* to invent new conventions, but instead to combine existing ones. It is based on W3C standards and reuses Java APIs where possible.

JAX-RS
------

Supports [JAX-RS](https://wikis.oracle.com/display/Jersey/Overview+of+JAX-RS+1.0+Features)-style RESTful API:

* resource annotations like `@Path` and `@GET`
* `UriBuilder` for building URIs out of components (includes implementation in JavaScript)
* `ResponseBuilder` for building `Response` objects

Further implementation of missing JAX-RS features is planned.

RDF API
-------

Supports [Jena](http://incubator.apache.org/jena/)-style object-oriented RDF API:

* `Model`
    * RDF/XML (DOM) serialization
    * Turtle serialization
* `Statement`
* `Resource`
* `Literal`

Utilities
---------

Includes utility classes for dealing with [SPARQL](http://www.w3.org/TR/sparql11-query/), [RDF/XML](http://www.w3.org/TR/REC-rdf-syntax/), and [XSLT](http://www.w3.org/TR/xslt):

* `Repository` for remote SPARQL 1.1 endpoint access
* `QueryBuilder` for building SPARQL queries
* `RDFForm` for reading requests in [RDF/POST](http://www.lsrn.org/semweb/rdfpost.html) encoding
* `MultipartParser` and `MultipartRequest` for reading `multipart/form-data` requests (PHP port of O'Reilly's [Multipart classes](http://www.servlets.com/cos/))
* `XSLTBuilder` for building XSLT transformations *(PHP's [XSL extension](http://php.net/manual/en/book.xsl.php) must be enabled)*
* `DOM2Model` for converting RDF/XML to Model (reverse of `Model::toDOM()`)

Usage
=====

To create a Graphity PHP application, you need to follow similar steps as in creating JAX-RS webapp, plus some extra steps because of PHP's interpreted and per-request nature:

1.  Checkout or extract graphity-core into `/lib/graphity-core` or similar folder in your project.
    We recommend choosing the latest version tag on GitHub.

    *We strongly recommend [Maven Standard Directory Layout](http://maven.apache.org/guides/introduction/introduction-to-the-standard-directory-layout.html), as it will be easier to share reusable resources with the Java version in the future.* It is used in the following examples.

2.  Create some resource class that imports and extends `Graphity\Resource`, for example:

        namespace My;

        use Graphity\Response;
        use Graphity\ResponseBuilder;
        use Graphity\View\ContentType;

        class Resource extends \Graphity\Resource

    *We strongly recommend using PHP namespaces with the [standard folder layout](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).* In Maven structure, that would be within the `src/main/php` folder). It is used in the following examples.

3.  Annotate the class with `@Path` and methods with `@GET`/`@POST` etc., for example:

        namespace My;

        use Graphity\Response;
        use Graphity\ResponseBuilder;
        use Graphity\View\ContentType;

        /** 
         * @Path("/hello")
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
                    entity("<html>Hello ". $this->getRequest()->getParameter("what") ."!</html>")->
                    status(Response::SC_OK)->
                    type(ContentType::TEXT_HTML)->
                    build();
            }
        }

    This class would match GET requests on `/hello` path and print a statement depending on the `what` query parameter value.

    *PHP annotations must be embedded in `/* */` comment blocks.*

    *`@Produces`/`@Consumes` annotations are not yet fully supported, but we recommend adding them for future compatibility.*

4.  Run `/lib/graphity-core/bin/route_mapper.php` specifying the root folder of your namespace and the location of your route map file, for example (paths are relative to project root in this case):

        $ php lib/graphity-core/bin/route_mapper.php src/main/php/My src/main/php/routes.php

    This should scan your resource classes and generate a route map file, which is used internally by Graphity to match request URIs against JAX-RS annotations.
    *This does not happen dynamically (as of yet), you have to re-map routes with `route_mapper.php` every time your annotations change.*

5. Implement a subclass of `Graphity\Application`:

        namespace My;

        class Application extends \Graphity\Application
        {

            public function __construct()
            {
                parent::__construct(include(dirname(dirname(__FILE__)) . "/routes.php"));

                $loader = new \Graphity\Loader("My", dirname(dirname(__FILE__)));
                $loader->register(); 
            }

        }

    This class initializes route map and `Graphity\Loader` and can be used for custom initializations.

6. Make an entry point to your `Application` like `index.php` and put it under `src/main/webapp`:

        define('ROOTDIR', dirname(dirname(dirname(dirname(__FILE__)))));

        require_once(ROOTDIR . '/lib/graphity-core/src/main/php/Graphity/Application.php');
        require_once(ROOTDIR . '/src/main/php/My/Application.php');

        $app = new My\Application();
        $app->run();

    The `Graphity\Application::run()` method will do the processing for you, executing the whole HTTP workflow from receiving a `Graphity\Request` to writing out a `Graphity\Response`.
    Later you might want to override it with `My\Application::run()` method to include a `try`/`catch` block for `Graphity\WebApplicationException` handling.

    *Both `Application` superclass and subclass need to be included here to bootstrap the framework.*

    *This should be the single and only entry point to your Graphity web application.*

7. Fix URL rewriting by adding `.htaccess` configuration under `src/main/webapp`:

        RewriteEngine On
        RewriteRule ^(.*)$ index.php/$1 [L]

    *Requests with `multipart/form-data` content type should not be accessed via PHP's `$_FILE` or similar methods, and instead used with Graphity's `MultipartRequest` and `MultipartParser` classes.*
    The following instructions make this possible by setting request content type to `multipart/form-data-alternate` before it is passed to PHP, and can be placed in `vhost.conf`:

        <Location />
            SetEnvIf Content-Type ^(multipart/form-data)(.*) NEW_CONTENT_TYPE=multipart/form-data-alternate$2 OLD_CONTENT_TYPE=$1$2
            RequestHeader set Content-Type %{NEW_CONTENT_TYPE}e env=NEW_CONTENT_TYPE
        </Location>

8. Ready? _Launch!_ Open [http://localhost/hello?what=world](http://localhost/hello?what=world) in your browser and you should see `Hello world!` printed out for you.
*Naturally the base URI in this example depends on your webserver and/or virtual host configuration.*

Documentation
=============

We need to do some work on this... Check out our [issues](https://github.com/Graphity/graphity-core/issues) so far.

Papers & presentations
----------------------

W3C ["Linked Enterprise Data Patterns" workshop](http://www.w3.org/2011/09/LinkedData/)

* [Graphity position paper](http://www.w3.org/2011/09/LinkedData/ledp2011_submission_1.pdf)
* [Graphity presentation](http://semantic-web.dk/presentations/LEDP2011.pdf)

License
=======

Graphity core is licensed under [Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0).

Libraries
=========

Graphity PHP core uses following 3rd party libraries:

1.  [Addendum](http://code.google.com/p/addendum/) (for annotation parsing)