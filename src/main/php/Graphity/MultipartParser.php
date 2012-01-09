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
 *  PHP rewrite of com.oreilly.servlet.multipart.MultipartParser Java class
 *  JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/MultipartParser.html
 *  source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/MultipartParser.java.htm
 */
class MultipartParser
{
    const DEFAULT_ENCODING = "ISO-8859-1";
    const LINE_END = "\r\n"; // \r\n??

    private $in = null;
    private $boundary = null;
    private $buf = array();
    private $encoding = self::DEFAULT_ENCODING;

    public function __construct(Request $req, $maxSize, $buffer = true, $limitLength = true, $encoding = null)
    {
        if ($encoding != null) $this->setEncoding($encoding);

        $type = $req->getContentType();

        if ($type == null || 0 !== strpos($type, "multipart/form-data"))
            throw new Exception("Posted content type isn't multipart/form-data");

        $length = $req->getContentLength();
        if ($length > $maxSize)
            throw new Exception("Posted content length of " . $length . " exceeds limit of " . $maxSize);

        $boundary = $this->extractBoundary($type);
        if ($boundary == null)
            throw new Exception("Separation boundary was not specified");

        /*
        // If required, wrap the real input stream with classes that 
        // "enhance" its behaviour for performance and stability
        if (buffer) {
            in = new BufferedServletInputStream(in);
        }
        if (limitLength) {
            in = new LimitedServletInputStream(in, length);
        }
        */

        $this->in = $req->getInputStream();
        $this->boundary = $boundary;
        do
        {
            $line = $this->readLine();

            if ($line == null)
                throw new Exception("Corrupt form data: premature ending");
            
            if (strpos($line, $boundary) === 0)
                break;
        }
        while (true);
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function readNextPart()
    {
        /*
        // Make sure the last file was entirely read from the input
        if (lastFilePart != null) {
        lastFilePart.getInputStream().close();
        lastFilePart = null;
        }
        */

        $headers = array();

        $line = $this->readLine();

        if ($line === null) return null;
        //else if (strlen($line) == 0) return null;
//var_dump($line);
        while ($line !== null && strlen($line) > 0)
        {
            $nextLine = null;
            $getNextLine = true;
            while ($getNextLine)
            {
                $nextLine = $this->readLine();

                if ($nextLine !== null && strpos($nextLine, " ") === 0 || strpos($nextLine, "\t") === 0)
                    $line .= $nextLine;
                else $getNextLine = false;
            }
//var_dump($line);
//print "!!!!!!!!!!!";
            $headers[] = $line;
            $line = $nextLine;
        }
//var_dump($headers);
        if ($line === null) return null;

        $name = null;
        $filename = null;
        $origname = null;
        $contentType = "text/plain";  // rfc1867 says this is the default
//var_dump($headers);
        foreach ($headers as $headerline)
        {
//var_dump($headerline);

            if (stripos($headerline, "content-disposition:") === 0)
            {
                $dispInfo = $this->extractDispositionInfo($headerline);
                // $disposition = $dispInfo[0];
//var_dump($dispInfo);
                $name = $dispInfo[1];
                $filename = $dispInfo[2];
                $origname = $dispInfo[3];
            }
            else if (stripos($headerline, "content-type:") === 0)
            {
                $type = $this->extractContentType($headerline);
                if ($type != null) $contentType = $type;
            }
        }
//var_dump($name);
        if ($filename == null) return new ParamPart($name, $this->in, $this->boundary, $this->encoding);
        else
        {
            if ($filename == "") $filename = null;
            $lastFilePart = new FilePart($name, $this->in, $this->boundary, $contentType, $filename, $origname);
            return $lastFilePart;
        }
    }

    private function extractBoundary($line)
    {
        $index = strrpos($line, "boundary=");
        if ($index === false) return null;

        $boundary = substr($line, $index + strlen("boundary="));

        /*
        if (boundary.charAt(0) == '"') {
            // The boundary is enclosed in quotes, strip them
            index = boundary.lastIndexOf('"');
            boundary = boundary.substring(1, index);
        }
        */

        $boundary = "--" . $boundary;

        return $boundary;
    }

    private function extractDispositionInfo($line)
    {
        $retval = array();

        $origline = $line;
        $line = strtolower($origline); // mb_strtolower() ?

        $start = strpos($line, "content-disposition: ");
        $end = strpos($line, ";");
        if ($start === false || $end === false)
            throw new Exception("Content disposition corrupt: " . $origline);

        $disposition = substr($line, $start + strlen("content-disposition: "), $end - ($start + strlen("content-disposition: ")));
        if ($disposition != "form-data")
            throw new Exception("Invalid content disposition: " . $disposition);

        $start = strpos($line, "name=\"", $end);
        $end = strpos($line, "\"", $start + strlen("name=\""));
        $startOffset = 6;
        /*
	    if (start == -1 || end == -1) {
            // Some browsers like lynx don't surround with ""
            // Thanks to Deon van der Merwe, dvdm@truteq.co.za, for noticing
            start = line.indexOf("name=", end);
            end = line.indexOf(";", start + 6);
            if (start == -1) {
                throw new IOException("Content disposition corrupt: "
                        + origline);
            } else if (end == -1) {
                end = line.length();
            }
            startOffset = 5; // without quotes we have one fewer char to skip
	    }
        */
        $name = substr($origline, $start + $startOffset, $end - ($start + $startOffset));
        
        $filename = null;
        $origname = null;

        $start = strpos($line, "filename=\"", $end + 1); // +2???
        $end = strpos($line, "\"", $start + strlen("filename=\""));
        if ($start !== false && $end !== false)
        {
            $filename = substr($origline, $start + strlen("filename=\""), $end - ($start + strlen("filename=\"")));
            $origname = $filename;
            /*
            // The filename may contain a full path.  Cut to just the filename.
            int slash = Math.max(filename.lastIndexOf('/'), filename
                    .lastIndexOf('\\'));
            if (slash > -1) {
                filename = filename.substring(slash + 1); // past last slash
            */
        }

        $retval[0] = $disposition;
        $retval[1] = $name;
        $retval[2] = $filename;
        $retval[3] = $origname;
        return $retval;
    }

    private function extractContentType($line)
    {
        $line = strtolower($line);
        
        $end = strpos($line, ";");
        if ($end === false) $end = strlen($line);
        
        return trim(substr($line, strlen("content-type:"), $end - strlen("content-type:")));
    }

    private function readLine()
    {
        $line = fgets($this->in);
        if ($line === false) return null;
        return rtrim($line); // Cut off the trailing \n or \r\n
    }

}
