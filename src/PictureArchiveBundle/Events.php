<?php

namespace PictureArchiveBundle;

/**
 *
 * @package PictureArchiveBundle
 * @author Moki <picture-archive@mokis-welt.de>
 */
final class Events
{
    const IMPORT_INITIALIZE = 'picture-archive.import.initialize';
    const IMPORT_FILE = 'picture-archive.import.file';
    const IMPORT_ANALYSIS_FAILED = 'picture-archive.import.analyse.failed';
    const IMPORT_SAVE_FAILED = 'picture-archive.import.save.failed';
    const IMPORT_ERROR = 'picture-archive.import.error';
    const IMPORT_SUCCESS = 'picture-archive.import.success';
    const IMPORT_FINISH = 'picture-archive.import.finish';
}
