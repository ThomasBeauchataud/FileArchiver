<?php

/*
 * Author Thomas Beauchataud
 * Since 02/05/2022
 */

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
     * @param string $fileName
     * @param DateTimeInterface|null $from
     * @return void
     * @throws FileArchiverException
     */
    public function clear(string $fileName, DateTimeInterface $from = null): void;

    /**
     * @param string $fileName
     * @param DateTimeInterface $dateTime
     * @return array
     * @throws FileArchiverException
     */
    public function find(string $fileName, DateTimeInterface $dateTime): array;

}