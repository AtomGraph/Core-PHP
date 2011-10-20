<?php

namespace Graphity\Form;

use Graphity\Response;
use Graphity\WebApplicationException;


class PostMultipartRDFForm extends MultipartRDFForm
{
    public function validate()
    {
        $errors = array();

        // check if we have the data
        if(count($this->getModel()->getStatements()) === 0)
            throw new WebApplicationException(Response::SC_BAD_REQUEST, "Form data is missing.");
        return $errors;
    }
}
