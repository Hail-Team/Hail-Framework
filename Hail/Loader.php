<?php
/**
 * Created by IntelliJ IDEA.
 * User: FlyingHail
 * Date: 2015/1/25 0025
 * Time: 20:04
 */

namespace Hail;

/**
 * PSR-4 Class Loader
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
 *
 */
class Loader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * prefix lists
     *
     * @param array $prefixes
     * @return void
     */
    public function addPrefixes($prefixes)
    {
        foreach ($prefixes as $prefix => $baseDir) {
            $this->addPrefix($prefix, $baseDir);
        }
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $baseDir A base directory for class files in the namespace.
     * @return void
     */
    public function addPrefix($prefix, $baseDir)
    {
        $prefix = trim($prefix, '\\').'\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = array_merge(
                $this->prefixes[$prefix],
                (array) $baseDir
            );
        } else {
            $this->prefixes[$prefix] = (array) $baseDir;
        }
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $class The fully-qualified class name.
     * @return string|null The mapped file name on success, or null on failure.
     */
    public function findFile($class)
    {
        $class = ltrim($class, '\\');
        foreach ($this->prefixes as $prefix => $baseDirs) {
            if (0 === strpos($class, $prefix)) {
                $classWithoutPrefix = str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    substr($class, strlen($prefix))
                );

                foreach ($baseDirs as $baseDir) {
                    $file = $baseDir . $classWithoutPrefix . '.php';
                    if (file_exists($file)) {
                        return $file;
                    }
                }
            }
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return bool True if the file exists, false if not.
     */
    public function loadClass($class)
    {
        $file = $this->findFile($class);
        if (null !== $file) {
            require $file;
            return true;
        }
        return false;
    }
}
