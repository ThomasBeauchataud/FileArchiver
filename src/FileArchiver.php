<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTimeInterface;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FileArchiver implements FileArchiverInterface
{

    /**
     * @var FilesystemOperator
     */
    private FilesystemOperator $flysystem;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param FilesystemOperator $flysystem
     * @param Filesystem $filesystem
     */
    public function __construct(FilesystemOperator $flysystem, Filesystem $filesystem = new Filesystem())
    {
        $this->flysystem = $flysystem;
        $this->filesystem = $filesystem;
    }


    /**
     * @inheritDoc
     */
    public function archive(string $filepath, DateInterval $duration): void
    {
        $filepath = Path::normalize(Path::canonicalize($filepath));

        if (!$this->filesystem->exists($filepath)) {
            throw new FileNotFoundException(null, 0, null, $filepath);
        }

        $filename = basename($filepath);
        $archiveInfo = ArchiveInfoFactory::buildFromFilenameAndDuration($filename, $duration);
        $this->flysystem->write($archiveInfo->getArchivedFilename(), file_get_contents($filepath));
        $this->filesystem->remove($filepath);
        $this->clear();
    }

    /**
     * @inheritDoc
     */
    public function clear(?DateTimeInterface $from = null): void
    {
        foreach ($this->flysystem->listContents('/', FilesystemReader::LIST_DEEP) as $item) {

            if (!$item->isFile()) {
                continue;
            }

            $path = $item->path();
            $archiveInfo = ArchiveInfoFactory::buildFromArchivedFilename($path);
            if ($archiveInfo->isExpired() || (null !== $from && $archiveInfo->getArchivedAt() < $from)) {
                $this->flysystem->delete($path);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $filename): bool
    {
        $filename = basename($filename);

        foreach ($this->flysystem->listContents('/', FilesystemReader::LIST_DEEP) as $item) {

            if (!$item->isFile()) {
                continue;
            }

            $path = $item->path();
            $archiveInfo = ArchiveInfoFactory::buildFromArchivedFilename(basename($path));
            if ($filename === $archiveInfo->getFilename()) {
                return true;
            }
        }

        return false;
    }


    /**
     * @inheritDoc
     */
    public function retrieve(string $filename, string $directory): array
    {
        $filename = basename($filename);
        $retrievedFiles = [];

        foreach ($this->flysystem->listContents('/', FilesystemReader::LIST_DEEP) as $item) {

            if (!$item->isFile()) {
                continue;
            }

            $path = $item->path();
            $archiveInfo = ArchiveInfoFactory::buildFromArchivedFilename($path);
            if ($filename === $archiveInfo->getFilename()) {
                if (!$this->filesystem->exists($directory)) {
                    $this->filesystem->mkdir($directory);
                }
                file_put_contents("$directory/$filename", $this->flysystem->read($path));
                $retrievedFiles[] = "$directory/$filename";
            }
        }

        return $retrievedFiles;
    }
}