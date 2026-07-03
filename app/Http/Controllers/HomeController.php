<?php

namespace App\Http\Controllers;

use App\Models\Galery;
use App\Models\Post;
use App\Models\Profile;

class HomeController extends Controller
{
    public function index() {
        $articles = Post::latest()->take(3)->get();
        $articlesWithNewProperty = $articles->map(function ($article) {
            $article->created_at_humans = $article->created_at->diffForHumans();
            $article->category_name = $article->category->category_name;
            $article->excerpt = strip_tags($article->excerpt);

            return $article;
        });
        echo json_encode([
            'articles' => $articlesWithNewProperty,
            'galeries' => Galery::all(),
            'profile'  => Profile::first()
        ]);
    }
}
