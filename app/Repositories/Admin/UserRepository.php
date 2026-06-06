<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\UserRepositoryInterface;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Log;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function all($term)
    {
        try {
            return $this->user
                ->when($term, function ($query) use ($term) {
                    return $query->where('name', 'like', '%' . $term . '%');
                })
                ->paginate(10);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            return $this->user->find($id);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function create(array $data)
    {
        try {
            $this->user->create($data);
            return true;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $user = $this->user->find($id);

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role_id = $data['role_id'];

            if (isset($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();
            return true;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->user->find($id);
            $user->delete();
            return true;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }
}
