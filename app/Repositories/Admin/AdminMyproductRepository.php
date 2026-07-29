<?php

namespace App\Repositories\Admin;

use App\Models\Myproduct;
use App\Repositories\Contracts\Admin\AdminMyproductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class AdminMyproductRepository implements AdminMyproductRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data produk berhasil dimuat.',
                'data' => Myproduct::latest()->get()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data produk gagal dimuat.'
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            if ($request->file('product_image')) {
                $data['product_image'] = $request->file('product_image')->store('post-images/product');
            }

            $product = Myproduct::create($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data produk berhasil ditambah.',
                'data' => $product
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data produk gagal ditambah.'
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            if ($data->product_image) {
                Storage::delete($data->product_image);
            }

            $data->delete();

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data produk berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data produk gagal dihapus.'
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data produk berhasil dimuat.',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data produk gagal dimuat.'
            ];
        }
    }

    public function edit(array $data, object $product, object $request)
    {
        try {
            if ($request->file('product_image')) {
                if ($product->product_image) {
                    Storage::delete($product->product_image);
                }
                $data['product_image'] = $request->file('product_image')->store('post-images/product');
            }

            $product->update($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data produk berhasil diubah.',
                'data' => $product->fresh()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data produk gagal diubah.'
            ];
        }
    }
}
