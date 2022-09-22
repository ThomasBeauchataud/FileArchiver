<?php

namespace TBCD\Tests\FileArchiver;

use DateInterval;
use DateTime;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem as Flysystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use TBCD\FileArchiver\FileArchiver;
use TBCD\FileArchiver\FileArchiverInterface;

class FileArchiverTest extends TestCase
{

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $dirList = [
            Path::canonicalize(__DIR__ . '/../var/tmp'),
            Path::canonicalize(__DIR__ . '/../var/archives'),
            Path::canonicalize(__DIR__ . '/../var/retrieves')
        ];

        foreach ($dirList as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir);
            } else {
                foreach (scandir($dir) as $file) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * @return void
     * @throws FilesystemException
     */
    public function testArchive(): void
    {
        $fileArchiver = $this->createFileArchiver();
        $file = $this->createRandomFile();
        $duration = new DateInterval('PT3H');
        $fileArchiver->archive($file, $duration);
        $this->assertFileDoesNotExist($file);
        $this->assertTrue($fileArchiver->has($file));
    }

    /**
     * @return void
     * @throws FilesystemException
     */
    public function testClear(): void
    {
        $fileArchiver = $this->createFileArchiver();
        $file = $this->createRandomFile();
        $fileArchiver->archive($file, new DateInterval('PT3H'));
        $fileArchiver->clear();
        $this->assertTrue($fileArchiver->has($file));
        $fileArchiver->clear(new DateTime());
        $this->assertFalse($fileArchiver->has($file));
        $file = $this->createRandomFile();
        $fileArchiver->archive($file, new DateInterval('PT1S'));
        sleep(2);
        $fileArchiver->clear();
        $this->assertFalse($fileArchiver->has($file));
    }

    /**
     * @return void
     * @throws FilesystemException
     */
    public function testRetrieve(): void
    {
        $fileArchiver = $this->createFileArchiver();
        $file = $this->createRandomFile();
        $fileArchiver->archive($file, new DateInterval('PT3H'));
        $retrievedFiles = $fileArchiver->retrieve(uniqid(), Path::canonicalize(__DIR__ . '/../var/retrieves'));
        $this->assertEmpty($retrievedFiles);
        $retrievedFiles = $fileArchiver->retrieve($file, Path::canonicalize(__DIR__ . '/../var/retrieves'));
        $this->assertNotEmpty($retrievedFiles);
        $this->assertFileExists($retrievedFiles[0]);
        $this->assertEquals(Path::canonicalize(__DIR__ . '/../var/retrieves'), Path::getDirectory($retrievedFiles[0]));
    }

    /**
     * @return string
     */
    private function createRandomFile(): string
    {
        $filepath = __DIR__ . '/../var/tmp/' . uniqid() . '.txt';
        file_put_contents($filepath, "test");
        return Path::canonicalize($filepath);
    }

    /**
     * @return FileArchiverInterface
     */
    private function createFileArchiver(): FileArchiverInterface
    {
        return new FileArchiver(new Flysystem(new LocalFilesystemAdapter(Path::canonicalize(__DIR__ . '/../var/archives'))));
    }
}