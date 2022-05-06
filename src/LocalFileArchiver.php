<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use TBCD\FileArchiver\Exception\FileArchiverException;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
class LocalFileArchiver implements FileArchiverInterface
{

    private Filesystem $filesystem;
    private Finder $finder;
    private string $workspace;

    /**
     * @param string|null $workspace
     */
    public function __construct(string $workspace = null)
    {
        $workspace = $workspace ?? getcwd() . "/archive";
        $this->workspace = Path::normalize($workspace);
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
    }


    /**
     * @inheritDoc
     */
    public function archive(string $filePath, DateInterval $duration, array $context = []): string
    {
        if (!$this->filesystem->exists($filePath)) {
            throw new FileArchiverException("Unable to find the file $filePath");
        }

        if (!$this->filesystem->exists($this->workspace)) {
            $this->filesystem->mkdir($this->workspace);
        }

        $now = (new DateTime());
        $fileName = Path::getFilenameWithoutExtension($filePath);
        $extension = Path::getExtension($filePath);
        $suffix = sprintf("_%s_%s", $now->format('YmdHis'), $now->add($duration)->format('YmdHis'));
        $newPath = sprintf("%s/%s%s.%s", $this->workspace, $fileName, $suffix, $extension);

        $this->filesystem->rename($filePath, $newPath);
        $this->clear();

        return $newPath;
    }

    /**
     * @throws Exception
     *
     * @inheritDoc
     */
    public function clear(DateTimeInterface $from = null): void
    {
        $now = new DateTime();
        $finder = $this->finder->files()->in($this->workspace);

        foreach ($finder as $file) {

            $name = Path::getFilenameWithoutExtension($file);
            $explode = explode('_', $name);
            $expiration = new DateTime(end($explode));
            if ($expiration < $now) {
                $this->filesystem->remove($file);
                continue;
            }

            if ($from) {
                $archiveDate = new DateTime(prev($explode));
                if ($archiveDate < $from) {
                    $this->filesystem->remove($file);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function find(string $fileName): array
    {
        $finder = $this->finder->files()->in($this->workspace)->name("$fileName*");
        return iterator_to_array($finder);
    }
}