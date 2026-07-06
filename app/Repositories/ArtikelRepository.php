<?php

namespace App\Repositories;

use App\Models\{Post, Profile};

use App\Repositories\Contracts\ArtikelRepositoryInterface;

class ArtikelRepository implements ArtikelRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return array{artikels: \Illuminate\Pagination\LengthAwarePaginator<int, Post>, page_title: string}
     */
    public function latest(array $data)
    {
        $artikels = Post::with(['category'])->latest();

        if(isset($data['search'])){
            $artikels->where('title','like', '%' . $data['search'] . '%');
        }

        $artikels = $artikels->paginate(6);

        return [
            'page_title' => 'Artikel',
            'artikels' => $artikels
        ];
    }

    /**
     * Summary of show
     * @param array $data
     * @return array{artikel: Post|\Illuminate\Database\Eloquent\Collection<int, Post>|\stdClass|null, page_title: string, profile: Profile|\stdClass|null}
     */
    public function show(array $data)
    {

        $profile = Profile::first();
        $singleArtikel = Post::with(['category'])->find($data['id']);
        $singleArtikel->created_at_humans = $singleArtikel->created_at->diffForHumans();

        return [
            'page_title' => 'Artikel',
            'profile' => $profile,
            'artikel' => $singleArtikel
        ];
    }
}
