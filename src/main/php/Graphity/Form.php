<?php
/**
 * Abstract class for sub-classing. Used to represent data submitted from a HTML form and access the Request parameters in a convenient way.
 *
 * @package     Graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

abstract class Form
{

    //protected $request = null;
    

    /**
     * Constructs Form from Request.
     */
    
    public abstract function __construct(Request $request);

    /**
     * Validates this form and returns array of errors.
     * @return array An array of Errors
     */
    
    public abstract function validate();

    /**
     * Return true if this form is multipart.
     *
     * @return boolean
     */
    public function isMultipart() {
        return false;
    }

}

