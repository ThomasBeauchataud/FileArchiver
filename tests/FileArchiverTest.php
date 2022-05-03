<?php

namespace TBCD\Tests\FileArchiver;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use TBCD\FileArchiver\Exception\FileArchiverException;
use TBCD\FileArchiver\FileArchiver;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
class FileArchiverTest extends TestCase
{

    /**
     * @return void
     * @throws FileArchiverException
     */
    public function testArchive(): void
    {
        $fileArchiver = new FileArchiver(sys_get_temp_dir() . '/archive');
        $file = $this->createRandomFile();
        $archiveFilePath = $fileArchiver->archive($file, new DateInterval('PT3H'));
        $this->assertTrue(file_exists($archiveFilePath));
        $this->assertFalse(file_exists($file));
        $fileArchiver->clear(new DateTime());
        $this->assertFalse(file_exists($archiveFilePath));
    }

    /**
     * @return void
     * @throws FileArchiverException
     */
    public function testFind(): void
    {
        $fileArchiver = new FileArchiver(sys_get_temp_dir() . '/archive');
        $file = $this->createRandomFile();
        $fileArchiver->archive($file, new DateInterval('PT3H'));
        $result = $fileArchiver->find(Path::getFilenameWithoutExtension($file));
        $this->assertNotEmpty($result);
    }

    /**
     * @return void
     * @throws FileArchiverException
     */
    public function testClear(): void
    {
        $fileArchiver = new FileArchiver(sys_get_temp_dir() . '/archive');
        $file = $this->createRandomFile();
        $archivedFile = $fileArchiver->archive($file, new DateInterval('PT3H'));
        $fileArchiver->clear();
        $this->assertTrue(file_exists($archivedFile));
        $fileArchiver->clear(new DateTime());
        $this->assertFalse(file_exists($archivedFile));
    }

    /**
     * @return string
     */
    private function createRandomFile(): string
    {
        $filePath = sys_get_temp_dir() . "/" . uniqid() . '.txt';
        file_put_contents($filePath, "test");
        return $filePath;
    }
}