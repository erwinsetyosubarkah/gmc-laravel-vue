<?php

namespace App\Repositories\Admin;

use App\Models\Event;
use App\Repositories\Contracts\Admin\AdminEventRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class AdminEventRepository implements AdminEventRepositoryInterface
{
    public function all()
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data event berhasil dimuat.',
                'data' => Event::latest()->get(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data event gagal dimuat.'
            ];
        }
    }

    public function store(array $data, object $request)
    {
        try {
            if ($request->file('event_image')) {
                $data['event_image'] = $request->file('event_image')->store('post-images/event');
            }

            $time = strtotime($data['event_date'] ?? '');
            if ($time !== false) {
                $data['event_date'] = date('Y-m-d H:i:s', $time);
            }

            $event = Event::create($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data event berhasil ditambah.',
                'data' => $event,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data event gagal ditambah.'
            ];
        }
    }

    public function destroy(object $data)
    {
        try {
            if ($data->event_image) {
                Storage::delete($data->event_image);
            }

            $data->delete();

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data event berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data event gagal dihapus.'
            ];
        }
    }

    public function showEdit(object $data)
    {
        try {
            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data event berhasil dimuat.',
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data event gagal dimuat.'
            ];
        }
    }

    public function edit(array $data, object $event, object $request)
    {
        try {
            $time = strtotime($data['event_date'] ?? '');
            if ($time !== false) {
                $data['event_date'] = date('Y-m-d H:i:s', $time);
            }

            if ($request->file('event_image')) {
                if ($event->event_image) {
                    Storage::delete($event->event_image);
                }
                $data['event_image'] = $request->file('event_image')->store('post-images/event');
            }

            $event->update($data);

            return [
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data event berhasil diubah.',
                'data' => $event->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'title' => 'Gagal',
                'message' => 'Data event gagal diubah.'
            ];
        }
    }
}
