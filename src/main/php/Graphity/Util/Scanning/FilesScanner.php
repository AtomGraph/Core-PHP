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
 *  @author         Julius Šėporaitis <julius@seporaitis.net>
 *  @link           http://graphity.org/
 */

namespace Graphity\Util\Scanning;

class FilesScanner implements Scanner
{

    /**
     * @var array
     */
    protected $listOfDirs = array();

    /**
     * @var boolean
     */
    protected $recursive = false;

    /**
     * @var ScannerListener
     */
    protected $listener = null;

    /**
     * Constructor
     * 
     * @param string $dirPath
     * @param boolean $recursive
     */
    public function __construct($dirPath, $recursive = false)
    {
        $this->listOfDirs[] = $dirPath;
        $this->recursive = $recursive;
    }

    /**
     * Scans the $dirPath and invokes ScannerListener::accept on the resources found.
     * 
     * @see Scanner::scan()
     */
    public function scan(ScannerListener $listener)
    {
        $this->listener = $listener;
        do {
            $this->scanDirectory(array_shift($this->listOfDirs));
        } while(count($this->listOfDirs) > 0);
    }

    /**
     * Scans a single directory.
     .
     * @param string $path
     * 
     * @return void
     */
    protected function scanDirectory($path)
    {
        $iterator = new DirectoryIterator($path);
        foreach($iterator as $item) {
            if($item->isDot() || ! $item->isReadable()) {
                continue;
            }
            
            if($item->isDir() && $this->recursive) {
                $this->listOfDirs[] = $item->getPathname();
            }
            
            if($item->isFile()) {
                if($this->listener->accept($item->getPathname())) {
                    $this->listener->process($item->getPathname());
                }
            }
        }
    }
}
