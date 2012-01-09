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

namespace Graphity\Repository\Dydra;

use Graphity\Repository;
use Graphity\WebApplicationException;

class Client extends Repository\Client
{
    /**
     * Dydra endpoint URL.
     */
    const DYDRA_ENDPOINT_URL = "http://dydra.com/";

    /**
     * @var string $authToken
     */
    private $authToken = null;

    public function __construct($endpointUrl, $authToken)
    {
        parent::__construct($endpointUrl);

        $this->authToken = $authToken;
    }

    /**
     * Prepare request URL.
     *
     * @return string
     */
    protected function getURL()
    {
        $url = parent::getURL();

        // Add authentication token at the end of URL.
        if(! strpos($url, "?")) {
            $url .= "?auth_token=" . $this->getAuthToken();
        } else {
            $url .= "&auth_token=" . $this->getAuthToken();
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }
}

