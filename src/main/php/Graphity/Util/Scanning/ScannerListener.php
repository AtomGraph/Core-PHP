<?php
/**
 * A listener for receiving events on resources from a Scanner.
 * 
 * @author Julius Šėporaitis <julius@seporaitis.net>
 */

namespace Graphity\Util\Scanning;

interface ScannerListener
{

    /**
     * Accept a scanned resource.
     * 
     * This method will be invoked by Scanner to ascertain if the listener accepts the resource for processing.
     * If acceptable then Scanner will then invoke process(...) method.
     * 
     * @param mixed $name
     * 
     * @return boolean
     */
    public function accept($name);

    /**
     * Process a scanned resource.
     * 
     * This method will be invoked after the listener has accepted the resource.
     * 
     * @param mixed $name
     * 
     * @return void
     */
    public function process($name);
}
