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

namespace Graphity;

/**
 * A utility class to handle multipart/form-data requests, the kind of requests that support file uploads.
 * Wraps PHP's functions and global arrays to emulate Java's MultipartRequest class.
 * http://www.servlets.com/cos/javadoc/com/oreilly/servlet/MultipartRequest.html
 * http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/MultipartRequest.java.htm
 */
class MultipartRequest implements RequestInterface
{
    const DEFAULT_MAX_POST_SIZE = 1048576; // 1 Meg

    private $request = null;
    private $saveDir = null;
    private $parser = null;
    protected $parameters = array();
    protected $files = array();

    /**
     * Constructs MultipartRequest from a simple Request.
     * @param Request $request
     * @param string $saveDir Directory to save files to
     */
    
    public function __construct(Request $request, $saveDir, $maxSize = null, $encoding = null)
    {
        if ($request == null)
            throw new Exception("$request cannot be null");
        if ($saveDir == null)
            throw new Exception("saveDirectory cannot be null");
        if ($maxSize === null)
            $maxSize = self::DEFAULT_MAX_POST_SIZE;
        if ($maxSize <= 0)
            throw new Exception("maxPostSize must be positive");
        $this->request = $request;

        $parser = new MultipartParser($request, $maxSize, true, true, $encoding);

//var_dump($request->getQueryString());

        while (($part = $parser->readNextPart()) != null)
            if ($part->getName() != null)
            {
                //$this->addKey($part->getName());
                if ($part->isParam())
                {
                    $existingValues = array();
                    if (isset($this->parameters[$part->getName()])) $existingValues = $this->parameters[$part->getName()];
                    else $this->parameters[$part->getName()] = $existingValues;
                    $existingValues[] = $part->getValue();
                    $this->parameters[$part->getName()] = $existingValues;
                }
                if ($part->isFile())
                    if ($part->getFileName() != null)
                    {
                        $this->files[$part->getName()] = new UploadedFile($saveDir, $part->getFileName(), $part->getFileName(), $part->getContentType()); // what about the original filename?
                        $part->writeTo($saveDir);
                    }
                    else
                        $this->files[$part->getName()] = new UploadedFile(null, null, null, null);
            }
    }

    /**
     * Returns the content type of an uploaded file, or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Content type
     */
    
    public function getFileContentType($name)
    {
        try
        {
            $file = $this->files[$name];
            return $file->getContentType();
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Returns the filesystem name of an uploaded file, or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Filesystem name
     */
    
    public function getFilesystemName($name)
    {
        try
        {
            $file = $this->files[$name];
            return $file->getFilesystemName();
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Returns the original name of an uploaded file (as supplied by the client browser), or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Original name
     */
    
    public function getOriginalFileName($name)
    {
        try
        {
            $file = $this->files[$name];
            return $file->getOriginalFilename();
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    public function getAttribute($name)
    {
        return $this->request->getAttribute($name);
    }

    public function setAttribute($name, $value)
    {
        $this->request->setAttribute($name, $value);
    }

    public function getContentType()
    {
        return $this->request->getContentType();
    }

    public function getContentLength()
    {
        return $this->request->getContentLength();
    }

    public function getCookies()
    {
        return $this->request->getCookies();
    }

    public function getParameter($name)
    {
        return $this->request->getParameter($name);
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function getHeader($name)
    {
        return $this->request->getHeader($name);
    }

    public function getSession()
    {
        return $this->request->getSession();
    }

    public function getParameterMap()
    {
        return $this->request->getParameterMap();
    }

    public function getPathInfo()
    {
        return $this->request->getPathInfo();
    }

    public function getRequestURI()
    {
        return $this->request->getRequestURI();
    }

    public function getServerName()
    {
        return $this->request->getServerName();
    }

    public function getServerPort()
    {
        return $this->request->getServerPort();
    }

    public function getScheme()
    {
        return $this->request->getScheme();
    }

    public function getQueryString()
    {
        return $this->request->getQueryString();
    }

	public function getInputStream()
    {
        return $this->request->getInputStream();
    }


}

?>
