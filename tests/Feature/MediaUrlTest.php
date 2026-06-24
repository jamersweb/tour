<?php

namespace Tests\Feature;

use App\Models\Package;
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
}
