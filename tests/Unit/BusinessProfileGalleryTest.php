<?php

namespace Tests\Unit;

use App\Models\BusinessProfile;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class BusinessProfileGalleryTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    public function test_gallery_photo_reference_is_replaced_with_local_path(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $tenant = Tenant::create(['owner_id' => $user->id]);

        $expectedPath = "tenants/{$tenant->id}/gallery/photo.jpg";

        $mediaService = Mockery::mock(MediaService::class);
        $mediaService->shouldReceive('downloadGooglePhoto')
            ->once()
            ->with('photo-ref', $tenant->id)
            ->andReturn($expectedPath);

        app()->instance(MediaService::class, $mediaService);

        $profile = BusinessProfile::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test İşletme',
            'slug' => 'test-isletme',
            'description' => 'Test açıklaması',
            'gallery' => [
                ['photo_reference' => 'photo-ref'],
            ],
        ]);

        $profile->refresh();

        $this->assertSame([$expectedPath], $profile->gallery);
    }
}
