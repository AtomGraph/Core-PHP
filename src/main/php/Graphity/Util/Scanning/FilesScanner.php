<?php

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
