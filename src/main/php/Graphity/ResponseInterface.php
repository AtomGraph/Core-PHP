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
 *  @author         Martynas Jusevičius <martynas@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity;

/**
 * Interface for all classes that want to implement HTTP responses.
 *
 * Response codes reference can be found here:
 *  - http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
 */
interface ResponseInterface
{

    /**
     *  The request has succeeded.
     */
    const SC_OK = 200;

    /**
     *  The server has fulfilled the request but does not need to return an entity-body, and might want 
     *  to return updated metainformation. The response MAY include new or updated metainformation in the 
     *  form of entity headers, which if present SHOULD be associated with the request variant.
     */
    const SC_NO_CONTENT = 204;

    /**
     * If the client has performed a conditional GET request and access is allowed, but the document
     * has not been modified, the server SHOULD respond with this status code.
     */
    const SC_NOT_MODIFIED = 304;

    /**
     * The request could not be understood by the server due to malformed syntax. 
     * The client SHOULD NOT repeat the request without modifications.
     */
    const SC_BAD_REQUEST = 400;

    /**
     * The request requires user authentication.
     */
    const SC_UNAUTHORIZED = 401;

    /**
     * The server understood the request, but is refusing to fulfill it. Authorization will not 
     * help and the request SHOULD NOT be repeated.
     */
    const SC_FORBIDDEN = 403;
    
    /**
     * The server has not found anything matching the Request-URI. No indication is given of whether 
     * the condition is temporary or permanent.
     */
    const SC_NOT_FOUND = 404;

    /**
     * The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.
     * The response MUST include an Allow header containing a list of valid methods for the requested resource.
     */
    const SC_METHOD_NOT_ALLOWED = 405;

    /**
     * The resource identified by the request is only capable of generating response entities which have
     * content characteristics not acceptable according to the accept headers sent in the request. 
     */
    const SC_METHOD_NOT_ACCEPTABLE = 406;

    /** 
     * The request could not be completed due to a conflict with the current state of the resource. 
     * This code is only allowed in situations where it is expected that the user might be able to 
     * resolve the conflict and resubmit the request. 
     */
    const SC_CONFLICT = 409;

    /**
     * The server encountered an unexpected condition which prevented it from fulfilling the request.
     */
    const SC_INTERNAL_SERVER_ERROR = 500;

    function addCookie(Cookie $cookie);

    function setHeader($name, $value);
    
    function getHeaders();

    function getStatus(); // not in Java Servlet API

    function setStatus($status);

    function getContentType(); // not in Java Servlet API

    function setContentType($contentType);

    function getCharacterEncoding();

    function setCharacterEncoding($encoding); // not in Java Servlet API

    function sendRedirect($url);

}

