<?php


namespace PictureArchiveBundle\Import;

class Exception extends \RuntimeException
{

    const FILE_EXISTS = 10;
    const HASH_EXISTS = 11;
    const FILE_UNSUPPORTED = 12;
}
