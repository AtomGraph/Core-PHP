<?php
/**
 * An abstract class for sub-classing by the actual view classes.
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

abstract class View extends Response
{

    private $resource = null;

    /**
     * Constructs View from Resource.
     * @param Resource $resource Resource
     */
    
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->setStatus(Response::SC_OK);
        $this->setCharacterEncoding("UTF-8");
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }
}

