<?php

/*
 * Author Thomas Beauchataud
 * Since 02/05/2022
 */

namespace TBCD\FileArchiver\Message;

use DateInterval;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
class ArchiveFile
{

    /**
     * @var string
     */
    private string $filePath;

    /**
     * @var DateInterval
     */
    private DateInterval $duration;

    /**
     * @var array
     */
    private array $context;

    /**
     * @param string $filePath
     * @param DateInterval $duration
     * @param array $context
     */
    public function __construct(string $filePath, DateInterval $duration, array $context = [])
    {
        $this->filePath = $filePath;
        $this->duration = $duration;
        $this->context = $context;
    }


    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return DateInterval
     */
    public function getDuration(): DateInterval
    {
        return $this->duration;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}