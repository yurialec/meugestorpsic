<?php

namespace App\Services\Tenants;

use App\Helpers\Utils;
use App\Repositories\Tenants\PatientRepository;
use Auth;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\UploadedFile;

class PatientService
{
    protected $PatientRepository;

    public function __construct(PatientRepository $PatientRepository)
    {
        $this->PatientRepository = $PatientRepository;
    }

    public function all($term)
    {
        return $this->PatientRepository->all($term);
    }

    public function create(array $data)
    {
        $patientData = [
            "group" => $data['group'],
            "gender" => $data['gender'],
            "full_name" => $data['full_name'],
            "cpf" => Utils::sanitizeInteger($data['cpf']),
            "email" => $data['email'],
            "phone" => Utils::sanitizeInteger($data['phone']),
            "date_of_birth" => $data['date_of_birth'],
        ];

        if (!session('is_admin')) {
            $patientData['employee_id'] = Auth::guard('employee')->id();
        }

        return $this->PatientRepository->create($patientData);
    }

    public function disable($id)
    {
        return $this->PatientRepository->disable($id);
    }

    public function getPatientById($id)
    {
        return $this->PatientRepository->getPatientById($id);
    }

    public function update($id, $data)
    {
        $patientData = [
            "group" => $data['group'],
            "gender" => $data['gender'],
            "full_name" => $data['full_name'],
            "cpf" => Utils::sanitizeInteger($data['cpf']),
            "email" => $data['email'],
            "phone" => Utils::sanitizeInteger($data['phone']),
            "date_of_birth" => $data['date_of_birth'],
        ];

        return $this->PatientRepository->update($id, $patientData);
    }
    public function upload(UploadedFile $file)
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $filePath = $file->getPathname();

        $reader->open($filePath);

        $imported = 0;
        $errors = [];

        $header = null;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                $cells = $row->getCells();
                $rowData = array_map(function ($cell) {
                    return $cell->getValue();
                }, $cells);

                if ($rowIndex === 1) {
                    $header = $rowData;
                    continue;
                }

                if (empty(array_filter($rowData))) {
                    continue;
                }

                $data[] = array_combine($header, $rowData);

            }
        }

        return $this->PatientRepository->upload($data);
    }
}