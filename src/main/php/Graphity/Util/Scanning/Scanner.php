<?php
/**
 * An interface for scanning resources and reporting those resources to a scanning listener.
 * 
 * @author Julius Šėporaitis <julius@seporaitis.net>
 */

namespace Graphity\Util\Scanning;

interface Scanner
{

    /**
     * Perform a scan and report to resource listener.
     *
     * @param ScannerListener $listener
     * 
     * @return void
     */
    public function scan(ScannerListener $listener);
}
