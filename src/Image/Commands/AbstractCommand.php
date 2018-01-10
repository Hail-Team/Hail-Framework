<?php

namespace Hail\Image\Commands;

abstract class AbstractCommand
{
    /**
     * Arguments of command
     *
     * @var array
     */
    public $arguments;

    /**
     * Output of command
     *
     * @var mixed
     */
    protected $output;

    /**
     * Executes current command on given image
     *
     * @param  \Hail\Image\Image $image
     * @return mixed
     */
    abstract public function execute($image);

    /**
     * Creates new command instance
     *
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Creates new argument instance from given argument key
     *
     * @param  integer $key
     * @return Argument
     */
    public function argument($key)
    {
        return new Argument($this, $key);
    }

    /**
     * Returns output data of current command
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output ?: null;
    }

    /**
     * Determines if current instance has output data
     *
     * @return bool
     */
    public function hasOutput()
    {
        return $this->output !== null;
    }

    /**
     * Sets output data of current command
     *
     * @param mixed $value
     */
    public function setOutput($value)
    {
        $this->output = $value;
    }
}
