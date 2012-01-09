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
 * http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/MultipartRequest.java.htm
 */
class UploadedFile
{
    private $dir = null;
    private $filename = null;
    private $original = null;
    private $type = null;

    public function __construct($dir, $filename, $original, $type)
    {
        $this->dir = $dir;
        $this->filename = $filename;
        $this->original = $original;
        $this->type = $type;
    }
    
    public function getContentType()
    {
        return $this->type;
    }

    public function getFilesystemName()
    {
        return $this->filename;
    }

    public function getOriginalFilename()
    {
        return $this->original;
    }

    public function getFile()
    {
        if ($this->dir !== null && $this->filename !== null)
            return null;
        else
            return fopen($this->dir . "/" . $this->filename); // DIRECTORY_SEPARATOR?
    }
}

?>
