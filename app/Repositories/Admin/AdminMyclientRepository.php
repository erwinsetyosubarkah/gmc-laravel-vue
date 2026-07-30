<?php

namespace App\Repositories\Admin;

use App\Models\Myclient;
use App\Repositories\Contracts\Admin\AdminMyclientRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class AdminMyclientRepository implements AdminMyclientRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data klien berhasil dimuat.',
                'data' => Myclient::latest()->get()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data klien gagal dimuat.'
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            if ($request->file('client_image')) {
                $data['client_image'] = $request->file('client_image')->store('post-images/client');
            }

            $client = Myclient::create($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data klien berhasil ditambah.',
                'data' => $client
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data klien gagal ditambah.'
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            if ($data->client_image) {
                Storage::delete($data->client_image);
            }

            $data->delete();

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data klien berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data klien gagal dihapus.'
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data klien berhasil dimuat.',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data klien gagal dimuat.'
            ];
        }
    }

    public function edit(array $data, object $client, object $request)
    {
        try {
            if ($request->file('client_image')) {
                if ($client->client_image) {
                    Storage::delete($client->client_image);
                }
                $data['client_image'] = $request->file('client_image')->store('post-images/client');
            }

            $client->update($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data klien berhasil diubah.',
                'data' => $client->fresh()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data klien gagal diubah.'
            ];
        }
    }
}
