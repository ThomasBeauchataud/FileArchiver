<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTimeInterface;
use TBCD\FileArchiver\Exception\FileArchiverException;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
interface FileArchiverInterface
{

    /**
     * @param string $filePath
     * @param DateInterval $duration
     * @param array $context
     * @return string
     * @throws FileArchiverException
     */
    public function archive(string $filePath, DateInterval $duration, array $context = []): string;

    /**
     * @param DateTimeInterface|null $from
     * @return void
     * @throws FileArchiverException
     */
    public function clear(DateTimeInterface $from = null): void;

    /**
     * @param string $fileName
     * @return array
     * @throws FileArchiverException
     */
    public function find(string $fileName): array;

}