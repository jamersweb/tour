<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Support\MediaUrl;
use Tests\TestCase;

class MediaUrlTest extends TestCase
{
    public function test_upload_media_urls_use_public_acute_domain(): void
    {
        $package = new Package([
            'hero_image_path' => 'packages/01KVCY3V9VGBFQBCYX1PX42MHX.jpg',
        ]);

        $this->assertSame(
            'https://acutetourism.ae/uploads/packages/01KVCY3V9VGBFQBCYX1PX42MHX.jpg',
            $package->hero_image_url,
        );
    }

    public function test_legacy_media_underscore_paths_are_normalized(): void
    {
        $this->assertSame(
            '/legacy-media/uploads/0000/7/2025/03/14/example.png',
            MediaUrl::normalize('/legacy_media/uploads/0000/7/2025/03/14/example.png'),
        );
    }

    public function test_dead_legacy_logo_urls_use_local_logo_asset(): void
    {
        $this->assertSame(
            '/images/acute-tourism-logo.png',
            MediaUrl::normalize('/legacy_media/uploads/0000/6/2025/03/19/5.png'),
        );
    }
}
