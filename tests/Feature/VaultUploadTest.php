<?php

namespace Tests\Feature;

use App\Models\User;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Covers the Cloudinary upload in VaultController against a mocked SDK,
 * so no request ever reaches the real Cloudinary API.
 */
class VaultUploadTest extends TestCase
{
    private const SECURE_URL = 'https://res.cloudinary.com/demo/raw/upload/v1/pdfs/doc.pdf';

    private MockInterface $uploadApi;

    protected function setUp(): void
    {
        parent::setUp();

        // Use a private in-memory database rather than whatever server .env points at.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate');

        $this->uploadApi = Mockery::mock(UploadApi::class);
        $cloudinary = Mockery::mock(Cloudinary::class);
        $cloudinary->shouldReceive('uploadApi')->andReturn($this->uploadApi);
        $this->app->instance(Cloudinary::class, $cloudinary);

        Sanctum::actingAs(User::forceCreate([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]));
    }

    public function test_create_vault_uploads_the_file_and_stores_the_secure_url(): void
    {
        $this->uploadApi->shouldReceive('upload')
            ->once()
            ->with(
                Mockery::on(fn ($path) => is_string($path) && is_file($path)),
                [
                    'resource_type' => 'auto',
                    'folder' => 'pdfs/',
                    'access_mode' => 'public',
                    'type' => 'upload',
                    'is_public' => true,
                ]
            )
            ->andReturn(new ApiResponse(['secure_url' => self::SECURE_URL], []));

        $this->post('/api/v1/createVault', [
            'name' => 'Contract',
            'type' => 'document',
            'content' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.content.docLink', self::SECURE_URL);
    }

    public function test_create_vault_without_a_file_does_not_upload(): void
    {
        $this->uploadApi->shouldNotReceive('upload');

        $this->postJson('/api/v1/createVault', [
            'name' => 'Login',
            'type' => 'password',
            'content' => ['username' => 'me', 'password' => 'secret'],
        ])->assertCreated();
    }

    public function test_the_sdk_is_built_from_the_cloudinary_url_config(): void
    {
        $this->app->forgetInstance(Cloudinary::class);
        config(['cloudinary.cloud_url' => 'cloudinary://key:secret@demo-cloud']);

        $cloudinary = $this->app->make(Cloudinary::class);

        $this->assertSame('demo-cloud', $cloudinary->configuration->cloud->cloudName);
        $this->assertSame($cloudinary, $this->app->make(Cloudinary::class));
    }
}
