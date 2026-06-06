<?php

namespace App\Services\Tenants;

use App\Repositories\Tenants\ConfigurationRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ConfigurationService
{
    protected $ConfigurationRepository;

    public function __construct(ConfigurationRepository $ConfigurationRepository)
    {
        $this->ConfigurationRepository = $ConfigurationRepository;
    }


    public function confirmFirstAccess()
    {
        return $this->ConfigurationRepository->confirmFirstAccess();
    }

    public function all()
    {
        return $this->ConfigurationRepository->all();
    }

    public function storeConsultationDuration($data)
    {
        return $this->ConfigurationRepository->storeConsultationDuration($data);
    }

    public function storeConsultationPrice($data)
    {
        return $this->ConfigurationRepository->storeConsultationPrice($data);
    }

    public function availablePlans()
    {
        return $this->ConfigurationRepository->availablePlans();
    }

    public function getConfig()
    {
        return $this->ConfigurationRepository->getConfig();
    }

    public function logoStore($data)
    {
        $tenantId = (string) session('tenant_id');
        $tenantId = preg_replace('/[^A-Za-z0-9_\-]/', '', $tenantId); // remove caracteres inválidos

        $directory = "tenant/{$tenantId}/logo";

        $files = Storage::disk('public')->files($directory);
        Storage::disk('public')->delete($files);

        $image = $data['image'];
        $image_urn = $image->store($directory, 'public');
        $data['logo'] = $image_urn;

        return $this->ConfigurationRepository->logoStore($data);
    }

    public function updateProfileConfiguration(array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password'], $data['password_confirmation']);
        }

        if (!empty($data['logo'])) {
            $tenantId = (string) session('tenant_id');
            $tenantId = preg_replace('/[^A-Za-z0-9_\-]/', '', $tenantId);
            $directory = "tenant/{$tenantId}/logo";

            $files = Storage::disk('public')->files($directory);
            Storage::disk('public')->delete($files);

            $data['logo_path'] = $data['logo']->store($directory, 'public');
            unset($data['logo']);
        }

        return $this->ConfigurationRepository->updateProfileConfiguration($data);
    }

    public function listSchedule()
    {
        return $this->ConfigurationRepository->listSchedule();
    }
}
