<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\SiteInterface;
use App\Models\Site\MainText;
use App\Models\Site\SiteAbout;
use App\Models\Site\SiteCarousel;
use App\Models\Site\SiteContact;
use App\Models\Site\SiteLogo;
use App\Models\Site\SiteSocialMedia;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Storage;

class SiteRepository implements SiteInterface
{
    public $mainText;
    public $about;
    public $carousel;
    public $contatct;
    public $logo;
    public $socialMedia;

    public function __construct(MainText $mainText, SiteAbout $about, SiteCarousel $carousel, SiteContact $contatct, SiteLogo $logo, SiteSocialMedia $socialMedia)
    {
        $this->mainText = $mainText;
        $this->about = $about;
        $this->carousel = $carousel;
        $this->contatct = $contatct;
        $this->logo = $logo;
        $this->socialMedia = $socialMedia;
    }

    public function all()
    {

        $main = $this->mainText->first();
        $about = $this->about->first();
        $carousel = $this->carousel->get();
        $contatct = $this->contatct->first();
        $logo = $this->logo->first();
        $socialMedia = $this->socialMedia->get();

        try {

            return [
                'main' => $main,
                'about' => $about,
                'carousel' => $carousel,
                'contatct' => $contatct,
                'logo' => $logo,
                'socialMedia' => $socialMedia,
            ];

        } catch (Exception $err) {
            Log::error('Erro ao listar registros Site', [$err->getMessage()]);
            return false;
        }
    }

    public function save($data)
    {
        DB::beginTransaction();

        try {

            $normalize = function ($value, bool $nullAsEmptyString = false) {
                if (is_array($value)) {
                    return $value;
                }

                if (is_string($value)) {
                    $value = trim($value);

                    if (
                        $value === '' ||
                        strtolower($value) === 'null' ||
                        strtolower($value) === 'undefined'
                    ) {
                        return $nullAsEmptyString ? '' : null;
                    }

                    return $value;
                }

                if ($value === null) {
                    return $nullAsEmptyString ? '' : null;
                }

                return $value;
            };

            $processImage = function ($model, $removeFlag, $fileInput, string $dir) {

                if (!empty($fileInput)) {
                    if (!empty($model->image)) {
                        Storage::disk('public')->delete($model->image);
                    }

                    $file = $fileInput;
                    $path = $file->store($dir, 'public');
                    $model->image = $path;
                    return;
                }

                if ($removeFlag && !empty($model->image)) {
                    Storage::disk('public')->delete($model->image);
                    $model->image = null;
                }
            };


            /*
            |--------------------------------------------------------------------------
            | LOGO
            |--------------------------------------------------------------------------
            */
            $logo = $this->logo->first() ?? new SiteLogo();

            $processImage(
                $logo,
                !empty($data['logo_image_remove']),
                $data['logo_image'] ?? null,
                'site/logo/images'
            );

            $logo->save();


            /*
            |--------------------------------------------------------------------------
            | ABOUT
            |--------------------------------------------------------------------------
            */
            $about = $this->about->first() ?? new SiteAbout();

            $about->title = $normalize($data['about_title'] ?? null);
            $about->description = $normalize($data['about_description'] ?? null);

            $processImage(
                $about,
                !empty($data['about_image_remove']),
                $data['about_image'] ?? null,
                'site/about'
            );

            $about->save();


            /*
            |--------------------------------------------------------------------------
            | CONTACT
            |--------------------------------------------------------------------------
            */
            $contact = $this->contatct->first() ?? new SiteContact();

            $fieldsMap = [
                'contact_phone' => 'phone',
                'contact_email' => 'email',
                'contact_city' => 'city',
                'contact_state' => 'state',
                'contact_address' => 'address',
                'contact_zipcode' => 'zipcode',
            ];

            foreach ($fieldsMap as $input => $attr) {
                if (array_key_exists($input, $data)) {
                    $contact->$attr = $normalize($data[$input] ?? null);
                }
            }

            $contact->save();


            /*
            |--------------------------------------------------------------------------
            | MAIN TEXT
            |--------------------------------------------------------------------------
            */
            $main = $this->mainText->first() ?? new MainText();

            $main->title = $normalize($data['main_title'] ?? null);
            $main->text = $normalize($data['main_text'] ?? null);

            $main->save();


            /*
            |--------------------------------------------------------------------------
            | CAROUSEL
            |--------------------------------------------------------------------------
            */

            $oldIds = [];
            if (isset($data['carousel_old'])) {
                $oldIds = is_array($data['carousel_old'])
                    ? $data['carousel_old']
                    : [$data['carousel_old']];
            }

            if (!empty($oldIds)) {
                $this->carousel->whereNotIn('id', $oldIds)->delete();
            } else {
                $this->carousel->query()->delete();
            }

            if (isset($data['carousel_new'])) {
                $newFiles = is_array($data['carousel_new'])
                    ? $data['carousel_new']
                    : [$data['carousel_new']];

                foreach ($newFiles as $file) {
                    if ($file) {
                        $path = $file->store('site/carousel', 'public');
                        $this->carousel->newQuery()->create([
                            'image' => $path,
                        ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SOCIAL
            |--------------------------------------------------------------------------
            */

            $socialList = [];

            if (!empty($data['social'])) {
                $socialList = json_decode($data['social'], true) ?: [];
            }

            $this->socialMedia->query()->delete();

            foreach ($socialList as $item) {
                $this->socialMedia->newQuery()->create([
                    'name' => $normalize($item['name'] ?? null, true),
                    'url' => $normalize($item['url'] ?? null, true),
                    'icon' => $normalize($item['icon'] ?? null, true),
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */
            DB::commit();

            return $this->all();

        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Erro ao salvar registros Site', [$err->getMessage()]);
            return false;
        }
    }
}