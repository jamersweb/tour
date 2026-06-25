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

    public function test_media_removal_controls_clear_hero_and_selected_gallery_images(): void
    {
        $package = new Package([
            'hero_image_path' => 'packages/current-hero.jpg',
            'gallery_images' => [
                'packages/gallery/keep.jpg',
                'packages/gallery/remove.jpg',
            ],
        ]);

        $data = MediaUpload::applyRemovalControls([
            'hero_image_path' => 'packages/current-hero.jpg',
            'gallery_images' => [
                'packages/gallery/keep.jpg',
                'packages/gallery/remove.jpg',
            ],
            'remove_hero_image' => true,
            'remove_gallery_images' => ['packages/gallery/remove.jpg'],
        ], $package);

        $this->assertArrayNotHasKey('remove_hero_image', $data);
        $this->assertArrayNotHasKey('remove_gallery_images', $data);
        $this->assertNull($data['hero_image_path']);
        $this->assertSame(['packages/gallery/keep.jpg'], $data['gallery_images']);
    }

    public function test_media_removal_controls_can_filter_existing_record_gallery_when_upload_field_is_missing(): void
    {
        $package = new Package([
            'gallery_images' => [
                'packages/gallery/keep.jpg',
                'packages/gallery/remove.jpg',
            ],
        ]);

        $data = MediaUpload::applyRemovalControls([
            'remove_gallery_images' => ['packages/gallery/remove.jpg'],
        ], $package);

        $this->assertSame(['packages/gallery/keep.jpg'], $data['gallery_images']);
    }
}
