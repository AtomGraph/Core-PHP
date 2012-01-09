<?php

/**
 * Loader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * Original SplClassLoader source code: https://gist.github.com/221634
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 *
 * Original code was cleaned up of _fileExtension and _namespaceSeparator,
 * because:
 *   1. these are static values throughout the project. If you use different php extension
 *      feel free to extend/update this class. If you use two different php extensions
 *      in your project, well... we don't support that.
 *   2. _namespaceSeparator is ALWAYS '\' in PHP 5.3. Backward compatibility for '_'
 *      is already coded on line 106. Whats the point?
 *   3. 'require' changed to 'include_once' for better performance. (http://arin.me/blog/php-require-vs-include-vs-require_once-vs-include_once-performance-test)
 *
 * @author Julius Šėporaitis <julius@graphity.org>
 */

namespace Graphity;

class Loader
{
    private $_namespace;
    private $_includePath;

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the
     * specified namespace.
     * 
     * @param string $ns The namespace to use.
     */
    public function __construct($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     * 
     * @param string $includePath
     */
    public function setIncludePath($includePath)
    {
        $this->_includePath = $includePath;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string $includePath
     */
    public function getIncludePath()
    {
        return $this->_includePath;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return void
     */
    public function loadClass($className)
    {
        if (null === $this->_namespace || ($this->_namespace.'\\') === substr($className, 0, strlen($this->_namespace.'\\'))) {
            //error_log($className);
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($className, '\\'))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . ".php";

            //if(file_exists(($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName) === false) {
            //    throw new \Graphity\Exception("Could not load: '{$className}'.");
            //}

            include_once ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}
