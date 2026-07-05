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

}
