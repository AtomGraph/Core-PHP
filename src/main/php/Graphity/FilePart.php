<?php

// PHP rewrite of com.oreilly.servlet.multipart.FilePart Java class
// JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/FilePart.html
// source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/FilePart.java.htm

namespace Graphity;

class FilePart extends Part
{
    private $tmpName = null;
    private $fileName = null;
    private $filePath = null;
    private $contentType = null;
    private $partInput = null;
    private $part = null;

    public function __construct($name, $in, $boundary,$contentType, $fileName, $filePath)
    {
        parent::__construct($name);
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->contentType = $contentType;
        $this->tmpName = uniqid() . "." . pathinfo($fileName, PATHINFO_EXTENSION); 
        // http://www.php.net/manual/en/function.stream-copy-to-stream.php
        //$this->partInput = new PartInputStream($in, $boundary);
        $this->part = rtrim(stream_get_line($in, 5242880, $boundary));
        stream_get_line($in, 1); stream_get_line($in, 1); // strip possibly following \r\n
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getTmpName()
    {
        return $this->tmpName;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getInputStream()
    {
        throw new Exception("getInputStream() not implemented, use writeTo() instead");
    }

    public function writeTo($fileOrDirectory)
    {
        $written = 0;
        
        if ($this->fileName !== null)
        {
            $file = null;
            if (is_dir($fileOrDirectory)) $file = rtrim($fileOrDirectory, "/") . "/" . $this->tmpName;
            else $file = $fileOrDirectory;

            $written = file_put_contents($file, $this->part);
        }
        
        return $written;
    }

    public function isFile()
    {
        return true;
    }
}
