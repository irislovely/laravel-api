<?php

namespace Tests\Feature;

use App\Models\Providers;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilesInteractionTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Providers::factory()->count(1)->create(['name'=>'Google']);
        Providers::factory()->count(1)->create(['name'=>'Snapchat']);
    }

    /** @test */
    public function test_can_get_providers_list()
    {
        $this->withoutExceptionHandling();        

        $response = $this->get('/api/providers');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_can_get_files_list()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/api/files');

        $response->assertStatus(200);
    }

    public function test_required_fields_when_create_file()
    {
        $this->withoutExceptionHandling(); 
        $response = $this->json('POST', '/api/files', ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJson([
                "success" => false,
                "message" => "Error submitting file",
                "errors" => [
                    "name" => ["The name field is required."],
                    "provider_id" => ["The provider id field is required."],
                    "file" => ["The file field is required."],
                ]
            ]);
    }

    /**
     * List of files and conditions to guarantee success response:
     * 
     * Google - provider_id => 1 :
     *    + .jpg | ratio 4:3 | < 2 mb size : 4-3-dummy-image7-1024x768.jpg
     *    + .mp4 | < 1 minutes long : Sample MP4 Video File for Testing.mp4
     *    + .mp3 | < 30 seconds long | < 5mb size : file_example_MP3_1MG.mp3
     * 
     * Snapchat - provider_id => 2 :
     *    + .jpg, .gif | ratio 16:9 | < 5mb in size : Sample-16x9-image_1080px.jpg | final_5daf40a52203ec001352c299_392370.gif
     *    + .mp4, .mov | < 50mb in size | < 5 minutes long : Sample MP4 Video File for Testing.mp4 | sample-mov-file.mov
     * 
     * Other files in the test folder can be used to play around, and to test failed response
     * 
     * Remember to change provider_id in data[] accordingly
     * 
     */
    /** @test */
    public function test_can_create_file_complete()
    {
        $this->withoutExceptionHandling();

        $file = $this->getUploadableFile(base_path("tests/testfiles/uploads/sample-mov-file.mov"));

        $data =[
            'name'   => $this->faker->text(25),
            'provider_id' => 2
        ];

        $response = $this->call('POST', '/api/files', $data, [], ['file' => $file], []);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "File successfully uploaded"]);        
    }

    /**
     * Get uploadable file.
     * 
     * Remember to change original_name & mime_type accordingly:
     * 
     * image/jpeg,video/mp4,audio/mpeg,image/gif,video/quicktime
     * 
     * @return UploadedFile
     */
    protected function getUploadableFile($file)
    {
        $dummy = file_get_contents($file);

        file_put_contents(base_path("tests/" . basename($file)), $dummy);

        $path = base_path("tests/" . basename($file));
        $original_name = 'sample-mov-file.mov';
        $mime_type = 'video/quicktime';
        $error = null;
        $test = true;

        $file = new UploadedFile($path, $original_name, $mime_type, $error, $test);

        return $file;
    }
}
