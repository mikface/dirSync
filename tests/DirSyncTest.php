<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DirSyncTest extends TestCase
{
    public function testCannotReadNonExistentJsonFile(): void
    {
        $dirSync = new \DirSync\DirSync();
        $this->expectException(\DirSync\Exception::class);

        $dirSync->fromFile('nonExistentFile.JSON');
    }

    public function testCreateDirectoriesOk(): void
    {
        $dirSync = new \DirSync\DirSync();
        $root = __DIR__ . DIRECTORY_SEPARATOR . "tmp";
        mkdir($root);
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . "./data/test.JSON";
        $dirSync->setRootDir($root);
        $dirSync->fromFile($filePath);
        $dirSync->sync();
        $this->assertEquals(scandir($root), ['.', '..', 'log', 'src', 'test', 'vendor']);
        $this->rmdirRec($root);
    }

    public function testCreateDirectoriesRemoveOnlyMode()
    {
        $dirSync = new \DirSync\DirSync();
        $root = __DIR__ . DIRECTORY_SEPARATOR . "tmp";
        mkdir($root);
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . "./data/test.JSON";
        $dirSync->setRootDir($root);
        $dirSync->fromFile($filePath);
        $dirSync->sync(\DirSync\DirSync::SYNC_REMOVE_ONLY);
        $this->assertEquals(scandir($root), ['.', '..']);
        $this->rmdirRec($root);
    }

    private function rmdirRec($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->rmdirRec("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}