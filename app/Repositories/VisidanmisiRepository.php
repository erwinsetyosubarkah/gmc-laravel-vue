<?php

namespace App\Repositories;

use App\Models\{ Visidanmisi};

use App\Repositories\Contracts\VisidanmisiRepositoryInterface;

/**
 * Summary of VisisdanmisiRepository
 */
class VisidanmisiRepository implements VisidanmisiRepositoryInterface
{

    /**
     * Summary of getVisidanmisi
     * @return array{page_title: string, visidanmisi: Visidanmisi|\stdClass|null}
     */
    public function getVisidanmisi()
    {

        $visidanmisi = Visidanmisi::first();

        return [
            'page_title' => 'Visi dan Misi',
            'visidanmisi' => $visidanmisi
        ];
    }

    /**
     * Summary of edit
     * @param array $data
     * @param object $obj
     * @param object $request
     * @return array
     */
    public function edit(array $data, object $obj, object $request)
    {
        $visidanmisi = Visidanmisi::first();

        if ($visidanmisi) {
            $visidanmisi->update($data);
        } else {
            $visidanmisi = Visidanmisi::create($data);
        }

        return [
            'status' => 'success',
            'message' => 'Data visi dan misi berhasil diubah',
            'data' => $visidanmisi,
        ];
    }

}
