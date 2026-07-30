<?php

namespace App\Repositories\Admin;

use App\Models\Galery;
use App\Repositories\Contracts\Admin\AdminGaleryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class AdminGaleryRepository implements AdminGaleryRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data galeri berhasil dimuat.',
                'data' => Galery::latest()->get()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data galeri gagal dimuat.'
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            if ($request->file('galery_image')) {
                $data['galery_image'] = $request->file('galery_image')->store('post-images/galery');
            }

            $galery = Galery::create($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data galeri berhasil ditambah.',
                'data' => $galery
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data galeri gagal ditambah.'
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            if ($data->galery_image) {
                Storage::delete($data->galery_image);
            }

            $data->delete();

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data galeri berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data galeri gagal dihapus.'
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data galeri berhasil dimuat.',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data galeri gagal dimuat.'
            ];
        }
    }

    public function edit(array $data, object $galery, object $request)
    {
        try {
            if ($request->file('galery_image')) {
                if ($galery->galery_image) {
                    Storage::delete($galery->galery_image);
                }
                $data['galery_image'] = $request->file('galery_image')->store('post-images/galery');
            }

            $galery->update($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data galeri berhasil diubah.',
                'data' => $galery->fresh()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data galeri gagal diubah.'
            ];
        }
    }
}
