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
            '/legacy-media/uploads/0000/6/2025/03/14/4-2.png',
            MediaUrl::normalize('/legacy_media/uploads/0000/6/2025/03/14/4-2.png'),
        );
    }
}
