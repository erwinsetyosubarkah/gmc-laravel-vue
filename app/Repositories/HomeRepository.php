<?php

namespace App\Repositories;

use App\Models\{Post, Profile, Galery};

use App\Repositories\Contracts\HomeRepositoryInterface;

class HomeRepository implements HomeRepositoryInterface
{

    public function home()
    {
        $articles = Post::latest()->take(3)->get();
        $articlesWithNewProperty = $articles->map(function ($article) {
            $article->created_at_humans = $article->created_at->diffForHumans();
            $article->category_name = $article->category->category_name;
            $article->excerpt = strip_tags($article->excerpt);

            return $article;
        });

        return [
            'articles' => $articlesWithNewProperty,
            'galeries' => Galery::all(),
            'profile'  => Profile::first()
        ];
    }

}
