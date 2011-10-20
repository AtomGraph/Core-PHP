<?php

namespace Graphity\Form;

use Graphity\Response;
use Graphity\WebApplicationException;

class PostRDFForm extends RDFForm
{
    public function validate()
    {
        $errors = array();

        // check if we have the data
        if(count($this->getModel()->getStatements()) === 0) {
            throw new WebApplicationException(Response::SC_BAD_REQUEST, "Form data is missing.");
        }

        return $errors;
    }
}
