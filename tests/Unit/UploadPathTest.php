<?php

namespace Tests\Unit;

use App\Filament\Support\MediaUpload;
use App\Models\Package;
use App\Support\UploadPath;
use PHPUnit\Framework\TestCase;

class UploadPathTest extends TestCase
{
    public function test_it_normalizes_old_upload_domain_urls_to_disk_paths(): void
    {
        $this->assertSame(
            'packages/gallery/01KVCY3V9XESFMC2P1VHJN31JP.jpg',
            UploadPath::normalize('https://new.acutetourism.org/uploads/packages/gallery/01KVCY3V9XESFMC2P1VHJN31JP.jpg'),
        );

        $this->assertSame(
            'packages/gallery/01KVCY3V9XESFMC2P1VHJN31JP.jpg',
            UploadPath::normalize('https:\/\/new.acutetourism.org\/uploads\/packages\/gallery\/01KVCY3V9XESFMC2P1VHJN31JP.jpg'),
        );
    }

    public function test_package_media_attributes_are_normalized_before_filament_reads_them(): void
    {
        $package = new Package([
            'hero_image_path' => 'https://new.acutetourism.org/uploads/packages/01KVCY3V9VGBFQBCYX1PX42MHX.jpg',
            'gallery_images' => [
                'https://new.acutetourism.org/uploads/packages/gallery/01KVCY3V9XESFMC2P1VHJN31JP.jpg',
            ],
        ]);

        $serialized = $package->attributesToArray();

        $this->assertSame('packages/01KVCY3V9VGBFQBCYX1PX42MHX.jpg', $serialized['hero_image_path']);
        $this->assertSame(['packages/gallery/01KVCY3V9XESFMC2P1VHJN31JP.jpg'], $serialized['gallery_images']);
        $this->assertSame($serialized['gallery_images'], MediaUpload::formatState($serialized['gallery_images']));
    }

    public function test_empty_filepond_state_clears_saved_media(): void
    {
        $this->assertNull(MediaUpload::dehydrateState(null, 'packages/current-hero.jpg'));
        $this->assertSame([], MediaUpload::dehydrateState([], [
            'packages/gallery/keep.jpg',
            'packages/gallery/remove.jpg',
        ]));
    }

    public function test_filepond_state_preserves_remaining_gallery_images_after_remove(): void
    {
        $this->assertSame(['packages/gallery/keep.jpg'], MediaUpload::dehydrateState([
            'packages/gallery/keep.jpg',
        ], [
            'packages/gallery/keep.jpg',
            'packages/gallery/remove.jpg',
        ]));
    }
}
