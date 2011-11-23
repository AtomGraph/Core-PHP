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

class XSLTBuilder {

    private $transformer = null;

    private $doc = null;

    private $resolver = null; // is it used?

    private $useXSLTCache = false;

    private $profileXSLT = true;
    
    protected function __construct()
    {
        if(extension_loaded('xslcache') && !defined('PHPUNIT') && $this->useXSLTCache)
            $this->transformer = new \xsltCache();
        else
            $this->transformer = new \XSLTProcessor();
        $this->transformer->registerPHPFunctions(); // for URL encoding etc

        if($this->profileXSLT === true && $this->useXSLTCache === false)
            $this->transformer->setProfiling("/tmp/xslt-profiler.txt");
    }

    protected static function newInstance()
    {
        return new self();
    }

    public static function fromDocument(\DOMDocument $doc)
    {
        return self::newInstance()->document($doc);
    }

    public static function fromStylesheet(\DOMDocument $stylesheet)
    {
        return self::newInstance()->stylesheet($stylesheet);
    }

    public static function fromStylesheetURI($uri)
    {
        $doc = new \DOMDocument();
        $doc->resolveExternals = true;
        $doc->substituteEntities = true;
        $doc->load($uri);
        return self::newInstance()->stylesheet($doc);
    }

    public function document(\DOMDocument $doc)
    {
        $doc->formatOutput = true;
        $this->doc = $doc;
        return $this;
    }

    public function stylesheet(\DOMDocument $stylesheet)
    {
        $this->transformer->importStylesheet($stylesheet);
        return $this;
    }

    public function parameter($namespace, $name, $value)
    {
        $this->transformer->setParameter($namespace, $name, $value);
        return $this;
    }

    public function build()
    {
        return $this->transformer->transformToDoc($this->doc);
    }

}
