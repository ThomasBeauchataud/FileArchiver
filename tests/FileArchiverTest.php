<?php

/*
 * Author Thomas Beauchataud
 * Since 02/05/2022
 */

namespace TBCD\Tests\FileArchiver;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use TBCD\FileArchiver\Exception\FileArchiverException;
use TBCD\FileArchiver\FileArchiver;

class FileArchiverTest extends TestCase
{

    /**
     * @return void
     * @throws FileArchiverException
     */
    public function testArchive(): void
    {
        $fileArchiver = new FileArchiver();
        $file = $this->createRandomFile();
        $archiveFilePath = $fileArchiver->archive($file, new DateInterval('PT3H'));
        $this->assertTrue(file_exists($archiveFilePath));
        $this->assertFalse(file_exists($file));
        $fileArchiver->clear(Path::getFilenameWithoutExtension($file), new DateTime());
        $this->assertFalse(file_exists($archiveFilePath));
    }

    /**
     * @return void
     * @throws FileArchiverException
     */
    public function testFind(): void
    {
        $fileArchiver = new FileArchiver();
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
        $fileArchiver = new FileArchiver();
        $file = $this->createRandomFile();
        $archivedFile = $fileArchiver->archive($file, new DateInterval('PT3H'));
        $fileArchiver->clear(Path::getFilenameWithoutExtension($archivedFile));
        $this->assertTrue(file_exists($archivedFile));
        $fileArchiver->clear(Path::getFilenameWithoutExtension($archivedFile), new DateTime());
        $this->assertFalse(file_exists($archivedFile));

        $now = new DateTime();
        $filePath = sprintf("%s_%s_%s.txt", uniqid(), $now->sub(new DateInterval('PT3H'))->format('YmdHis'), $now->sub(new DateInterval('PT1H'))->format('YmdHis'));
        $file = fopen($filePath, 'w+');
        fwrite($file, 'test');
        fclose($file);
        $fileArchiver->clear(Path::getFilenameWithoutExtension($filePath));
        $this->assertFalse(file_exists($filePath));
    }

    /**
     * @return string
     */
    private function createRandomFile(): string
    {
        $filePath = uniqid() . '.txt';
        $file = fopen($filePath, 'w+');
        fwrite($file, 'test');
        fclose($file);
        return $filePath;
    }
}