<?php
/**
 * Request processing error. Mostly used when a form does not validate or business logic conditions are not satisfied.
 * 
 * @package		    graphity
 * @author		    Martynas Jusevicius <pumba@xml.lt>
 * @link		    http://www.xml.lt
 */

namespace Graphity;

class Error
{

    /**
     * @var string $name       Error name
     */
    private $name = null;

    /**
     * @var string $desc       Error description
     */
    private $desc = null;

    /**
     * Constructs Error from name.
     *
     * @param string $name Name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns name of this Error.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name of this Error.
     *
     * @param string $name Name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns description of this Error.
     *
     * @return string Description
     */
    public function getDescription()
    {
        return $this->desc;
    }

    /**
     * Sets description of this Error.
     *
     * @param string $desc Description
     */
    public function setDescription($desc)
    {
        $this->desc = $desc;
    }
}
