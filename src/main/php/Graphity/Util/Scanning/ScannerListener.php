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
 *  @author         Julius Šėporaitis <julius@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity\Util\Scanning;

/**
 * A listener for receiving events on resources from a Scanner.
 */
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
