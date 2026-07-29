<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use App\Repositories\Contracts\Admin\AdminCategoryRepositoryInterface;

class AdminCategoryRepository implements AdminCategoryRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status'  => 'success',
                'title'   => 'Berhasil',
                'message' => 'Data kategori berhasil dimuat.',
                'data'    => Category::latest()->get(),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'title'   => 'Gagal',
                'message' => 'Data kategori gagal dimuat.',
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            $category = Category::create($data);

            return [
                'status'  => 'success',
                'title'   => 'Berhasil',
                'message' => 'Data kategori berhasil ditambah.',
                'data'    => $category,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'title'   => 'Gagal',
                'message' => 'Data kategori gagal ditambah.',
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            $data->delete();

            return [
                'status'  => 'success',
                'title'   => 'Berhasil',
                'message' => 'Data kategori berhasil dihapus.',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'title'   => 'Gagal',
                'message' => 'Data kategori gagal dihapus.',
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status'  => 'success',
                'title'   => 'Berhasil',
                'message' => 'Data kategori berhasil dimuat.',
                'data'    => $data,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'title'   => 'Gagal',
                'message' => 'Data kategori gagal dimuat.',
            ];
        }
    }

    public function edit(array $data, object $category, object $request)
    {
        try {
            $category->update($data);

            return [
                'status'  => 'success',
                'title'   => 'Berhasil',
                'message' => 'Data kategori berhasil diubah.',
                'data'    => $category->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'title'   => 'Gagal',
                'message' => 'Data kategori gagal diubah.',
            ];
        }
    }
}
