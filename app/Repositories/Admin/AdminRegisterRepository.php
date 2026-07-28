<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\Contracts\Admin\AdminRegisterRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AdminRegisterRepository implements AdminRegisterRepositoryInterface
{
    public function all()
    {
        return [];
    }

    public function store(array $data, object $request)
    {
        unset($data['password2']);
        $data['password'] = Hash::make($data['password']);
        $created = User::create($data);

        if ($created) {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Pendaftaran Berhasil !',
            ];
        }

        return [
            'status' => 'error',
            'title' => 'Gagal',
            'message' => 'Pendaftaran gagal. Silakan coba lagi.',
        ];
    }

    public function destroy(object $data)
    {
        return [];
    }

    public function showEdit(object $data)
    {
        return [];
    }

    public function edit(array $data, object $obj, object $request)
    {
        return [];
    }
}
