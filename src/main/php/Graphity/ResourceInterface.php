<?php

namespace Graphity;

interface ResourceInterface
{
    const MULTIPART_FORM = "multipart/form-data-alternate"; // comes from vhost.conf

    // same as getAbsolutePath()
    function getURI();

    // Get the path of the current request relative to the base URI as a string
    function getPath();

    // Get the path segment.
    //function getPathSegment();
    

    //function getController();

    //function setController(Controller $controller);

    //function getResponse();

    //function setResponse(Response $response);

    //function doGet(Request $request, Response $response);

    //function doPost(Request $request, Response $response);

    //function doPut(Request $request, Response $response);

    //function doDelete(Request $request, Response $response);

}

