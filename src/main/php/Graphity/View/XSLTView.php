<?php
/**
 * Specialized view subclass that handles XSLT processor initialization and executes general view procedures, such as passing parameters/arguments to the XSLT stylesheet.
 * It is an abstract class for sub-classing. An application Views should extend the XSLTView.
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity\View;

use Graphity\View;
use Graphity\ResourceInterface;
use Graphity\Util\URIResolver;

abstract class XSLTView extends View
{
    private $doc = null;

    private $styleSheet = null;

    private $transformer = null;

    private $resolver = null;

    private $useXSLTCache = false;

    /**
     * Constructs View from Resource, initializes XSLT processor and creates URI resolver.
     * @param Resource $resource Resource
     */
    
    public function __construct(ResourceInterface $resource)
    {
        parent::__construct($resource);
        $this->doc = new \DOMDocument();
        $this->doc->formatOutput = true;
        $this->styleSheet = new \DOMDocument();
        $this->styleSheet->resolveExternals = true;
        $this->styleSheet->substituteEntities = true;
        $this->styleSheet->load($this->getStyleSheetPath()); // overridable method call in constructor!
        if(extension_loaded('xslcache') && !defined('PHPUNIT') && $this->useXSLTCache)
            $this->transformer = new \xsltCache();
        else
            $this->transformer = new \XSLTProcessor();
        $this->transformer->registerPHPFunctions(); // for URL encoding etc
        if(extension_loaded('xslcache') && !defined('PHPUNIT') && $this->useXSLTCache)
            $this->transformer->importStyleSheet($this->getStyleSheetPath());
        else
            $this->transformer->importStyleSheet($this->getStyleSheet());
    }

    protected function getStyleSheetPath()
    {
        throw new \WebApplicationException("Forbidden execution path.");
    }

    public function transform()
    {
        return $this->getTransformer()->transformToXML($this->getDocument());
    }

    public function getDocument()
    {
        return $this->doc;
    }

    public function setDocument(\DOMDocument $doc)
    {
        $this->doc = $doc;
    }

    public function getStyleSheet()
    {
        return $this->styleSheet;
    }

    public function setStyleSheet(\DOMDocument $styleSheet)
    {
        $this->styleSheet = $styleSheet;
    }

    public function getTransformer()
    {
        return $this->transformer;
    }

    public function getResolver()
    {
        return $this->resolver;
    }

    public function setResolver(URIResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getRequest()
    {
        return $this->getResource()->getRequest();
    }

    protected function applyParameters()
    {
    }
}

