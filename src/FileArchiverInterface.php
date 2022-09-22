<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTimeInterface;
use Exception;
use League\Flysystem\FilesystemException;

interface FileArchiverInterface
{

    /**
     * @param string $filepath
     * @param DateInterval $duration
     * @return void
     * @throws FilesystemException
     * @throws Exception
     */
    public function archive(string $filepath, DateInterval $duration): void;

    /**
     * @param DateTimeInterface|null $from
     * @return void
     * @throws FilesystemException
     * @throws Exception
     */
    public function clear(?DateTimeInterface $from = null): void;

    /**
     * @param string $filename
     * @return bool
     * @throws FilesystemException
     * @throws Exception
     */
    public function has(string $filename): bool;

    /**
     * @param string $filename
     * @param string $directory
     * @throws FilesystemException
     * @throws Exception
     */
    public function retrieve(string $filename, string $directory): array;

}