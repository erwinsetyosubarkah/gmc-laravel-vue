<?php

namespace App\Repositories;

use App\Models\{Galery};

use App\Repositories\Contracts\GaleryRepositoryInterface;

class GaleryRepository implements GaleryRepositoryInterface
{


    public function latest(array $data)
    {
        $galeries = Galery::latest();

        if(isset($data['search'])){
            $galeries->where('image_title','like', '%' . $data['search'] . '%');
        }

        $galeries = $galeries->paginate(6);

        return [
            'page_title' => 'Galery',
            'galeries' => $galeries
        ];
    }

}
