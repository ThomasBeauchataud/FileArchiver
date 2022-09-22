<?php

namespace TBCD\FileArchiver;

use DateTime;
use DateTimeInterface;

class ArchiveInfo
{

    /**
     * @var string
     */
    public string $filename;

    /**
     * @var string
     */
    public string $archivedFilename;

    /**
     * @var DateTimeInterface
     */
    public DateTimeInterface $archivedAt;

    /**
     * @var DateTimeInterface
     */
    public DateTimeInterface $expiresAt;

    /**
     * @param string $filename
     * @param string $archivedFilename
     * @param DateTimeInterface $archivedAt
     * @param DateTimeInterface $expiresAt
     */
    public function __construct(string $filename, string $archivedFilename, DateTimeInterface $archivedAt, DateTimeInterface $expiresAt)
    {
        $this->filename = $filename;
        $this->archivedFilename = $archivedFilename;
        $this->archivedAt = $archivedAt;
        $this->expiresAt = $expiresAt;
    }


    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getArchivedFilename(): string
    {
        return $this->archivedFilename;
    }

    /**
     * @return DateTimeInterface
     */
    public function getArchivedAt(): DateTimeInterface
    {
        return $this->archivedAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiresAt < (new DateTime());
    }
}