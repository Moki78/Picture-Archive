<?php

namespace PictureArchiveBundle\Component;

/**
 * Created by PhpStorm.
 * User: moki
 * Date: 29.06.17
 * Time: 21:53
 */
class FileCollection implements \Iterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    private $collection = [];

    /**
     * FileCollection constructor.
     */
    public function __construct() {
        $this->position = 0;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->collection[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->collection[$this->position]);
    }

}
