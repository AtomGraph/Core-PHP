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

namespace Graphity\Vocabulary;

use Graphity\Rdf;

class HttpStatusCodes
{
    const NS = "http://www.w3.org/2011/http-statusCodes#";

    // Informational status codes

    const Continue_ = "http://www.w3.org/2011/http-statusCodes#Continue"; // continue is reserved keyword in PHP

    const SwitchingProtocols = "http://www.w3.org/2011/http-statusCodes#SwitchingProtocols";

    const Processing = "http://www.w3.org/2011/http-statusCodes#Processing";

    // Successful status codes

    const OK = "http://www.w3.org/2011/http-statusCodes#OK";

    const Created = "http://www.w3.org/2011/http-statusCodes#Created";

    const Accepted = "http://www.w3.org/2011/http-statusCodes#Accepted";

    const NonAuthoritativeInformation = "http://www.w3.org/2011/http-statusCodes#NonAuthoritativeInformation";

    const NoContent = "http://www.w3.org/2011/http-statusCodes#NoContent";

    const ResetContent = "http://www.w3.org/2011/http-statusCodes#ResetContent";

    private static $model = null;

    public static function getModel() // can be made private when Rdf\Resource::getModel() is implemented
    {
        if (self::$model == null)
        {
            self::$model = new Rdf\Model();

            self::$model->addStatement(new Rdf\Statement(self::Continue_(), Http::statusCodeNumber(), new Rdf\Literal(100)));
            self::$model->addStatement(new Rdf\Statement(self::SwitchingProtocols(), Http::statusCodeNumber(), new Rdf\Literal(101)));
            self::$model->addStatement(new Rdf\Statement(self::Processing(), Http::statusCodeNumber(), new Rdf\Literal(102)));

            self::$model->addStatement(new Rdf\Statement(self::OK(), Http::statusCodeNumber(), new Rdf\Literal(200)));
            self::$model->addStatement(new Rdf\Statement(self::Created(), Http::statusCodeNumber(), new Rdf\Literal(201)));
            self::$model->addStatement(new Rdf\Statement(self::Accepted(), Http::statusCodeNumber(), new Rdf\Literal(202)));
            self::$model->addStatement(new Rdf\Statement(self::NonAuthoritativeInformation(), Http::statusCodeNumber(), new Rdf\Literal(200)));
            self::$model->addStatement(new Rdf\Statement(self::NoContent(), Http::statusCodeNumber(), new Rdf\Literal(201)));
            self::$model->addStatement(new Rdf\Statement(self::ResetContent(), Http::statusCodeNumber(), new Rdf\Literal(202)));
        }

        return self::$model;
    }

    public static function getURI()
    {
        return self::NS;
    }

    public static function NS()
    {
        //return self::getModel()->createResource(self::NS);
        return new Rdf\Resource(self::NS);
    }

    public static function Continue_()
    {
        //return self::getModel()->createResource(self::Continue_);
        return new Rdf\Resource(self::Continue_);
    }

    public static function SwitchingProtocols()
    {
        //return self::getModel()->createResource(self::SwitchingProtocols);
        return new Rdf\Resource(self::SwitchingProtocols);
    }

    public static function Processing()
    {
        //return self::getModel()->createResource(self::Processing);
        return new Rdf\Resource(self::Processing);
    }

    public static function OK()
    {
        //return self::getModel()->createResource(self::OK);
        return new Rdf\Resource(self::OK);
    }

    public static function Created()
    {
        //return self::getModel()->createResource(self::Created);
        return new Rdf\Resource(self::Created);
    }

    public static function Accepted()
    {
        //return self::getModel()->createResource(self::Accepted);
        return new Rdf\Resource(self::Accepted);
    }

    public static function NonAuthoritativeInformation()
    {
        //return self::getModel()->createResource(self::NonAuthoritativeInformation);
        return new Rdf\Resource(self::NonAuthoritativeInformation);
    }

    public static function NoContent()
    {
        //return self::getModel()->createResource(self::NoContent);
        return new Rdf\Resource(self::NoContent);
    }

    public static function ResetContent()
    {
        //return self::getModel()->createResource(self::ResetContent);
        return new Rdf\Resource(self::ResetContent);
    }

}
