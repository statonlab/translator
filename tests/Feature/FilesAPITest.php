<?php

namespace Tests\Feature;

use App\File;
use App\Helpers\FileHelper;
use App\Platform;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class FilesAPITest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set the headers.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->withHeader('Accept', 'application/json');
    }

    /** @test */
    public function testThatGuestsCantAccessFilesAPI()
    {
        // Not logged in
        $this->get('/web/files')->assertStatus(401);
    }

    /** @test */
    public function testThatNonAdminsCantAccessFilesAPI()
    {
        $this->actingAs($this->makeUser());
        $this->get('/web/files')->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanListFiles()
    {
        $this->actingAs($this->makeAdminUser());

        factory(File::class)->create();

        $this->get('/web/files')->assertSuccessful()->assertJsonStructure([
            'data' => [
                [
                    'name',
                    'id',
                    'app_version',
                    'platform',
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatGuestsCantCreateAFile()
    {
        $this->post('/web/files')->assertStatus(401);
    }

    /** @test */
    public function testThatUsersCantCreateAFile()
    {
        $this->actingAs($this->makeUser());
        $this->post('/web/files')->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanCreateFiles()
    {
        $this->actingAs($this->makeAdminUser());

        Storage::fake('files');

        $file_name = uniqid().'.json';

        $file = UploadedFile::fake()->create($file_name);
        file_put_contents($file->path(), '{}');
        $response = $this->post('/web/files', [
            'app_version' => 'v1.0.0',
            'platform_id' => factory(Platform::class)->create()->id,
            'file' => $file,
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'name',
            'id',
            'platform' => ['name'],
        ]);

        $file = File::find($response->json()['id']);

        $helper = new FileHelper($file->path);
        Storage::disk('files')->assertExists($helper->name());
    }

    /** @test */
    public function testThatGuestsCantUpdateFile()
    {
        $file = factory(File::class)->create();
        $this->put("/web/file/$file->id")->assertStatus(401);
    }

    /** @test */
    public function testThatAdminsCanUpdateFiles()
    {
        $this->actingAs($this->makeAdminUser());

        $file = factory(File::class)->create();

        $response = $this->put('/web/file/'.$file->id, [
            'app_version' => 'test_version_1',
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'name',
            'id',
            'platform' => ['name'],
        ]);

        $file = File::find($response->json()['id']);

        $this->assertEquals('test_version_1', $file->app_version);
    }

    /** @test */
    public function testGuestsCantDeleteFile()
    {
        $file = factory(File::class)->create();
        $this->delete("/web/file/$file->id")->assertStatus(401);
    }

    public function testAdminsCanDeleteAFile()
    {
        $this->actingAs($this->makeAdminUser());

        Storage::fake('files');

        // Create a file
        $file_name = uniqid().'.json';

        $file = UploadedFile::fake()->create($file_name);
        file_put_contents($file->path(), '{}');
        $response = $this->post('/web/files', [
            'app_version' => 10,
            'platform_id' => factory(Platform::class)->create()->id,
            'file' => $file,
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'name',
            'id',
            'platform' => ['name'],
        ]);

        // Now delete the file
        $file = File::find($response->json()['id']);

        $this->delete("/web/file/$file->id")->assertSuccessful();

        $helper = new FileHelper($file->path);
        Storage::disk('files')->assertMissing($helper->name());
    }

    public function testThatAdminsCanDownloadAFile()
    {
        $this->actingAs($this->makeAdminUser());

        Storage::fake('files');

        // Create a file
        $file_name = uniqid().'.json';
        $file = UploadedFile::fake()->create($file_name);
        file_put_contents($file->path(), '{}');
        $response = $this->post('/web/files', [
            'app_version' => 'v1.0.0',
            'platform_id' => factory(Platform::class)->create()->id,
            'file' => $file,
        ]);

        $response->assertSuccessful();

        $file = $response->json();

        $this->get("/download/file/{$file['id']}")->assertSuccessful();
    }
}
