<?php

/*
 * Author Thomas Beauchataud
 * Since 02/05/2022
 */

namespace TBCD\FileArchiver\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TBCD\FileArchiver\Exception\FileArchiverException;
use TBCD\FileArchiver\FileArchiverInterface;
use TBCD\FileArchiver\Message\ArchiveFile;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
#[AsMessageHandler]
class ArchiveFileHandler
{

    private FileArchiverInterface $fileArchiver;

    /**
     * @param FileArchiverInterface $fileArchiver
     */
    public function __construct(FileArchiverInterface $fileArchiver)
    {
        $this->fileArchiver = $fileArchiver;
    }


    /**
     * @param ArchiveFile $archiveFile
     * @return void
     * @throws FileArchiverException
     */
    public function __invoke(ArchiveFile $archiveFile): void
    {
        $this->fileArchiver->archive($archiveFile->getFilePath(), $archiveFile->getDuration(), $archiveFile->getContext());
    }
}