<?php

/*
 * Author Thomas Beauchataud
 * Since 02/05/2022
 */

namespace TBCD\FileArchiver;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use TBCD\FileArchiver\Exception\FileArchiverException;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
class FileArchiver implements FileArchiverInterface
{

    private Filesystem $filesystem;
    private Finder $finder;
    private string $workspace;
    private bool $restrictedRotation;

    /**
     * @param string $workspace
     * @param bool $restrictedRotation
     * @param ParameterBagInterface|null $parameterBag
     */
    public function __construct(string $workspace = __DIR__, bool $restrictedRotation = true, ParameterBagInterface $parameterBag = null)
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();

        $this->restrictedRotation = $restrictedRotation;

        if ($parameterBag && $parameterBag->has('kernel.project_dir')) {
            $workspace = $parameterBag->get('kernel.project_dir') . "\\var\\archive\\";
        }
        $this->workspace = Path::getDirectory(realpath($workspace));
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
        $this->clear($this->restrictedRotation ? $fileName : '');

        return $newPath;
    }

    /**
     * @inheritDoc
     */
    public function find(string $fileName, DateTimeInterface $dateTime = null): array
    {
        $finder = $this->finder->files()->in($this->workspace)->name("$fileName*");

        if ($dateTime) {
            if ($dateTime->format('His') === '000000') {
                $finder->name(sprintf('_%s_', $dateTime->format('Ymd')));
            } else {
                $finder->name(sprintf('_%s_', $dateTime->format('YmdHis')));
            }
        }

        return iterator_to_array($finder);
    }

    /**
     * @throws Exception
     *
     * @inheritDoc
     */
    public function clear(string $fileName, DateTimeInterface $from = null): void
    {
        $now = new DateTime();

        $finder = $this->finder->files()->in($this->workspace)->name("$fileName*");

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
}