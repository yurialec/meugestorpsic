<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Subscription;
use Exception;
use Log;

class SubscriptionRepository
{
    protected $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function all()
    {
        try {
            return $this->subscription->with(['plan', 'tenant.client'])->paginate(10);
        } catch (Exception $err) {
            Log::error('Erro', ['erro' => $err->getMessage()]);
            return null;
        }
    }

    public function find($id)
    {
        return Subscription::find($id);
    }

    public function create(array $data)
    {
        return Subscription::create($data);
    }

    public function update($id, array $data)
    {
        $model = Subscription::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return Subscription::destroy($id);
    }
}