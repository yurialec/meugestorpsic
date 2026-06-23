<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Payment;

class PaymentsRepository
{
    public function all()
    {
        return Payment::with(['subscription.tenant.client', 'plan'])
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function find($id)
    {
        return Payment::find($id);
    }

    public function create(array $data)
    {
        return Payment::create($data);
    }

    public function update($id, array $data)
    {
        $model = Payment::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return Payment::destroy($id);
    }
}
