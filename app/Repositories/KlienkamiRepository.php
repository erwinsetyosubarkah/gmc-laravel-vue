<?php

namespace App\Repositories;

use App\Models\{Myclient};

use App\Repositories\Contracts\KlienkamiRepositoryInterface;

/**
 * Summary of KlienkamiRepository
 */
class KlienkamiRepository implements KlienkamiRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return array{klienkami: \Illuminate\Pagination\LengthAwarePaginator<int, Myclient>, page_title: string}
     */
    public function latest(array $data)
    {
        $klienkami = Myclient::latest();

        if(isset($data['search'])){
            $klienkami->where('client_name','like', '%' . $data['search'] . '%');
        }

        $klienkami = $klienkami->paginate(6);

        return [
            'page_title' => 'Klien Kami',
            'klienkami' => $klienkami
        ];
    }

}
