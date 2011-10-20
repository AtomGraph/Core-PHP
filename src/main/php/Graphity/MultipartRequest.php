<?php
/**
 * A utility class to handle multipart/form-data requests, the kind of requests that support file uploads.
 * Wraps PHP's functions and global arrays to emulate Java's MultipartRequest class.
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

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
