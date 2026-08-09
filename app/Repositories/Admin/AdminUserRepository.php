<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\Contracts\Admin\AdminUserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data user berhasil dimuat.',
                'data' => User::latest()->get()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data user gagal dimuat.'
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            unset($data['password2']);
            $defaultPassword = '12345678';
            $rawPassword = $data['password'] ?? $defaultPassword;
            $data['password'] = Hash::make(hash('sha256', $rawPassword));

            if ($request->file('photo')) {
                $data['photo'] = $request->file('photo')->store('post-images/user');
            }

            $user = User::create($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data user berhasil ditambah.',
                'data' => $user
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data user gagal ditambah.'
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            $data->delete();

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data user berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data user gagal dihapus.'
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data user berhasil dimuat.',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data user gagal dimuat.'
            ];
        }
    }

    public function edit(array $data, object $user, object $request)
    {
        try {
            unset($data['password2']);


            $rawPassword = $data['password'];

            if (array_key_exists('password', $data) && $data['password'] !== null && $data['password'] !== '') {
                $data['password'] = Hash::make(hash('sha256', $rawPassword));
            } else {
                $rawPassword = '12345678';
                $data['password'] = Hash::make(hash('sha256', $rawPassword));
            }

            if ($request->file('photo')) {
                if ($user->photo) {
                    Storage::delete($user->photo);
                }

                $data['photo'] = $request->file('photo')->store('post-images/user');
            }

            $user->update($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data user berhasil diubah.',
                'data' => $user->fresh()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data user gagal diubah.'
            ];
        }
    }
}
