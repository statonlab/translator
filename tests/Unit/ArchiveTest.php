<?php

namespace Tests\Unit;

use App\Services\Storage\Archive;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ArchiveTest extends TestCase
{
    /**
     * @var Filesystem
     */
    protected $disk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = Storage::fake('translated');
    }

    /**
     * @param int $count
     * @return array|\Illuminate\Support\Facades\File
     */
    public function makeFiles($count = 1)
    {
        $files = [];
        for ($i = 0; $i < $count; $i++) {
            $name = uniqid();
            $file = UploadedFile::fake()->create($name);
            $path = \Storage::disk('translated')->putFile($name, $file);
            $files[] = \Storage::disk('translated')->path($path);
        }

        if ($count === 1) {
            return $files[0];
        }

        return $files;
    }

    /** @test */
    public function testGettingFiles()
    {
        $file = $this->makeFiles();
        $archive = new Archive([$file], $this->disk);
        $this->assertEquals(1, $archive->files()->count());
    }

    /** @test */
    public function testAddingAFile()
    {
        $file = $this->makeFiles();
        $archive = new Archive([], $this->disk);
        $archive->add($file);
        $this->assertEquals(1, $archive->files()->count());
    }

    /** @test */
    public function testCheckingIfFilesExist()
    {
        $files = $this->makeFiles(2);

        $zip = new Archive($files, $this->disk);
        $this->assertTrue($zip->filesExist());
    }

    /** @test */
    public function testZippingAFile()
    {
        $files = $this->makeFiles(5);

        $archive = new Archive($files, $this->disk);
        $zip_path = $archive->zip();
        $this->assertTrue(File::exists($zip_path));

        $zip = new \ZipArchive();
        $zip->open($zip_path, \ZipArchive::CHECKCONS);
        $files_count = $zip->numFiles;
        $zip->close();
        $this->assertEquals(count($files), $files_count);
    }
}
