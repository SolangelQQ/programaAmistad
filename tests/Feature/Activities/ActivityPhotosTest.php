<?php

namespace Tests\Feature\Activities;

use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityPhotosTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $activity;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->activity = Activity::create([
            'title' => 'Actividad con Fotos',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'created_by' => $this->user->id,
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function photo_upload_requires_authentication()
    {
        $file = UploadedFile::fake()->image('test-photo.jpg');

        $response = $this->post("/activities/{$this->activity->id}/photos", [
            'photos' => [$file]
        ]);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function photo_upload_validates_file_is_image()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->post("/activities/{$this->activity->id}/photos", [
            'photos' => [$file]
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }

    /** @test */
    public function photo_upload_validates_file_size()
    {
        $this->actingAs($this->user);

        // Crear archivo de 3MB (mayor al límite de 2MB)
        $file = UploadedFile::fake()->image('large-photo.jpg')->size(3000);

        $response = $this->post("/activities/{$this->activity->id}/photos", [
            'photos' => [$file]
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }
}