<?php

namespace App\Repositories;

use App\Models\{Myproduct};

use App\Repositories\Contracts\ProdukkamiRepositoryInterface;

/**
 * Summary of ProdukkamiRepository
 */
class ProdukkamiRepository implements ProdukkamiRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return array{page_title: string, produkkami: \Illuminate\Pagination\LengthAwarePaginator<int, Myproduct>}
     */
    public function latest(array $data)
    {
        $produkkami = Myproduct::latest();

        if(isset($data['search'])){
            $produkkami->where('product_name','like', '%' . $data['search'] . '%');
        }

        $produkkami = $produkkami->paginate(6);

        return [
            'page_title' => 'Produk Kami',
            'produkkami' => $produkkami
        ];
    }

    /**
     * Summary of show
     * @param array $data
     * @return array{page_title: string, produkkami: Myproduct|\Illuminate\Database\Eloquent\Collection<int, Myproduct>|\stdClass|null}
     */
    public function show(array $data)
    {

        $produkkami = Myproduct::find($data['id']);
        $produkkami->created_at_humans = $produkkami->created_at->diffForHumans();

        return [
            'page_title' => 'Produk Kami',
            'produkkami' => $produkkami
        ];
    }
}
