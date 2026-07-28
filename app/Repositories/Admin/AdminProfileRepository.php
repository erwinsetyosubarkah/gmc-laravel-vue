<?php

namespace App\Repositories\Admin;

use App\Repositories\Contracts\Admin\AdminProfileRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class AdminProfileRepository implements AdminProfileRepositoryInterface
{
    public function all()
    {
        return [];
    }

    public function store(array $data, object $request)
    {
        return [];
    }

    public function destroy(object $data)
    {
        return [];
    }

    public function showEdit(object $data)
    {
        return [];
    }

    public function edit(array $data, object $profile, object $request)
    {
        try {
            if ($request->file('club_logo')) {
                if ($request->old_club_logo) {
                    Storage::delete($request->old_club_logo);
                }
                $data['club_logo'] = $request->file('club_logo')->store('post-images/profile');
            }
            $id = $data['id'];
            unset($data['old_club_logo']);
            unset($data['id']);
            $updated = $profile->where('id',$id)->update($data);

            if ($updated) {
                return [
                    'status' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Data profil berhasil diubah !'
                ];
            }

            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data profil gagal diubah.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data profil gagal diubah.'
            ];
        }
    }
}
