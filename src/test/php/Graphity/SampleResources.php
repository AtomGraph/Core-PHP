<?php

use Graphity\Resource;

/** SOME DEPENDENCIES */
class TestResource extends Graphity\Resource { 
    public function exists() {
        return true;
    }

    public function describe() {
        return "";
    }

    /**
     *  @GET
     *  @Produces("text/html")
     *  @Produces("application/rdf+xml")
     */
    public function doGet() {
        return null;
    }

    /**
     *  @POST
     *  @Consumes("application/x-www-form-urlencoded")
     *  @Produces("text/html")
     *  @Produces("application/json")
     */
    public function doPost() {
        return null;
    }

    /**
     *  @DELETE
     */
    public function doDelete() {
        return null;
    }
}

//if(!class_exists('FrontPageResource', false)) {
    class FrontPageResource extends TestResource { }
//}
//if(!class_exists('AdminFrontPageResource', false)) {
    class AdminFrontPageResource extends TestResource { }
//}
//if(!class_exists('PostResource', false)) {
    class PostResource extends TestResource { }
//}
//if(!class_exists('PostListResource', false)) {
    class PostListResource extends TestResource { }
//}
//if(!class_exists('PostRandomResource', false)) {
    class PostRandomResource extends TestResource { }
//}
/** /SOME DEPENDENCIES */

