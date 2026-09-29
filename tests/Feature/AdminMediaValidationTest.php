<?php

namespace Tests\Feature;

use App\Filament\Resources\BusTourListings\Schemas\BusTourListingForm;
use App\Filament\Resources\BusTourPages\Schemas\BusTourPageForm;
use App\Filament\Resources\Experiences\Schemas\ExperienceForm;
use App\Filament\Resources\Packages\Schemas\PackageForm;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\StaticPages\Schemas\StaticPageForm;
use App\Filament\Resources\Tours\Schemas\TourForm;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminMediaValidationTest extends TestCase
{
    public static function mediaFields(): array
    {
        return [
            [BusTourListingForm::class, 'card_image_url'],
            [BusTourListingForm::class, 'detail_image_url'],
            [BusTourPageForm::class, 'choice_media_image_url'],
            [BusTourPageForm::class, 'choice_media_video_url'],
            [BusTourPageForm::class, 'private_image_url'],
            [ExperienceForm::class, 'hero_video_url'],
            [PackageForm::class, 'hero_video_url'],
            [StaticPageForm::class, 'hero_image_url'],
            [TourForm::class, 'hero_video_url'],
        ];
    }

    #[DataProvider('mediaFields')]
    public function test_media_fields_allow_local_paths_and_web_urls(string $form, string $name): void
    {
        $fields = $form::configure(Schema::make(new EditSiteSetting))->getFlatFields(withHidden: true);
        $field = $fields[$name];

        // Native URL inputs reject local paths before the form can reach the server.
        $this->assertSame('text', $field->getType());
        $rules = $field->getValidationRules();

        foreach (['/images/photo.jpg', '/videos/tour.mp4', '/legacy-media/uploads/photo.jpg', 'https://example.com/media.mp4', 'http://example.com/photo.jpg', null] as $value) {
            $this->assertTrue(Validator::make([$name => $value], [$name => $rules])->passes(), (string) $value);
        }

        foreach (['not a url', 'javascript:alert(1)', '//example.com/image.jpg', '/\\example.com/image.jpg', '/images/bad path.jpg'] as $value) {
            $this->assertTrue(Validator::make([$name => $value], [$name => $rules])->fails(), $value);
        }
    }
}
