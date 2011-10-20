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
 */
class MultipartRequest
{

    private $saveDir = null;

    /**
     * Constructs MultipartRequest from a simple Request.
     * @param Request $request
     * @param string $saveDir Directory to save files to
     */
    
    public function __construct(Request $request, $saveDir)
    {
        $this->saveDir = $saveDir;
    }

    /**
     * Returns the content type of an uploaded file, or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Content type
     */
    
    public function getContentType($fileParam)
    {
        if(isset($_FILES[$fileParam]) && $_FILES[$fileParam]["error"] != UPLOAD_ERR_NO_FILE)
            return $_FILES[$fileParam]["type"];
        else
            return null;
    }

    /**
     * Returns the filesystem name of an uploaded file, or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Filesystem name
     */
    
    public function getFilesystemName($fileParam)
    {
        if(isset($_FILES[$fileParam]) && $_FILES[$fileParam]["error"] != UPLOAD_ERR_NO_FILE)
            return $_FILES[$fileParam]["tmp_name"];
        else
            return null;
    }

    /**
     * Returns the original name of an uploaded file (as supplied by the client browser), or null if the file was not included in the upload.
     * @param string $fileParam The name of the file input in HTML
     * @return string Original name
     */
    
    public function getOriginalFileName($fileParam)
    {
        if(isset($_FILES[$fileParam]) && $_FILES[$fileParam]["error"] != UPLOAD_ERR_NO_FILE)
            return $_FILES[$fileParam]["name"];
        else
            return null;
    }
}

?>
