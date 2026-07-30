<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GaleryController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KlienkamiController;
use App\Http\Controllers\KontakkamiController;
use App\Http\Controllers\ProdukkamiController;
use App\Http\Controllers\VisidanmisiController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminGaleryController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminMyclientController;
use App\Http\Controllers\Admin\AdminRegisterController;
use App\Http\Controllers\Admin\AdminMyproductController;
use App\Http\Controllers\Admin\AdminVisidanmisiController;
use App\Models\Category;
use App\Models\Event;
use App\Models\Profile;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route web
Route::prefix('web')->group(function () {
    Route::get('/gethome', [HomeController::class,'index']);
    Route::get('/getprofile', [ProfileController::class,'index']);
    Route::get('/getvisidanmisi', [VisidanmisiController::class,'index']);
    Route::get('/getprodukkami', [ProdukkamiController::class,'index']);
    Route::get('/getprodukkami/{id}', [ProdukkamiController::class,'show']);
    Route::get('/getartikel', [ArtikelController::class,'index']);
    Route::get('/getartikel/{id}', [ArtikelController::class,'show']);
    Route::get('/getevent', [EventController::class,'index']);
    Route::get('/getevent/{id}', [EventController::class,'show']);
    Route::get('/getgalery', [GaleryController::class,'index']);
    Route::get('/getklienkami', [KlienkamiController::class,'index']);
    Route::get('/getkontakkami', [KontakkamiController::class,'index']);
});

Route::prefix('auth')->group(function () {
    // Route::get('/auth-login', [AdminLoginController::class,'index'])->name('login')->middleware('guest');
    Route::get('/check', [AdminLoginController::class,'check'])->name('check');
    Route::get('/login', function () {
        return view('auth.auth',[
            'profile'  => Profile::first()
        ]);
    })->name('login')->middleware('guest');
    Route::post('/login', [AdminLoginController::class,'authenticate'])->middleware('guest');
    Route::post('/logout', [AdminLoginController::class,'logout'])->middleware('auth');
    Route::post('/register', [AdminRegisterController::class,'store'])->middleware('guest');
});


// route Admin
Route::prefix('admin')->group(function () {
    // Route::get('/admin/dashboard',  [AdminHomeController::class,'index'])->middleware('auth');


    // Route::get('/admin/profile', [AdminProfileController::class,'index'])->middleware('admin');
    Route::post('/profile', [AdminProfileController::class,'edit'])->middleware('admin');

    // Route::get('/admin/visidanmisi', [AdminVisidanmisiController::class,'index'])->middleware('admin');
    Route::post('/visidanmisi', [AdminVisidanmisiController::class,'edit'])->middleware('admin');

    Route::get('/myproduct-all', [AdminMyproductController::class,'all'])->middleware('admin');
    Route::post('/myproduct', [AdminMyproductController::class,'store'])->middleware('admin');
    Route::delete('/myproduct/{myproduct}', [AdminMyproductController::class,'destroy'])->middleware('admin');
    Route::get('/myproduct-edit/{myproduct}', [AdminMyproductController::class,'showedit'])->middleware('admin');
    Route::post('/myproduct-edit/{myproduct}', [AdminMyproductController::class,'edit'])->middleware('admin');

    Route::get('/category-all', [AdminCategoryController::class,'all'])->middleware('admin');
    Route::post('/category', [AdminCategoryController::class,'store'])->middleware('admin');
    Route::delete('/category/{category}', [AdminCategoryController::class,'destroy'])->middleware('admin');
    // Route::get('/admin-category-alldata', [AdminCategoryController::class,'allData'])->middleware('admin');
    Route::get('/category-edit/{category}', [AdminCategoryController::class,'showedit'])->middleware('admin');
    Route::post('/category-edit/{category}', [AdminCategoryController::class,'edit'])->middleware('admin');

    Route::get('/post-all', [AdminPostController::class,'all'])->middleware('auth');
    Route::post('/post', [AdminPostController::class,'store'])->middleware('auth');
    Route::delete('/post/{post}', [AdminPostController::class,'destroy'])->middleware('auth');
    Route::get('/post-edit/{post}', [AdminPostController::class,'showedit'])->middleware('auth');
    Route::post('/post-edit/{post}', [AdminPostController::class,'edit'])->middleware('auth');

    Route::get('/event-all', [AdminEventController::class,'all'])->middleware('admin');
    Route::post('/event', [AdminEventController::class,'store'])->middleware('admin');
    Route::delete('/event/{event}', [AdminEventController::class,'destroy'])->middleware('admin');
    Route::get('/event-edit/{event}', [AdminEventController::class,'showedit'])->middleware('admin');
    Route::post('/event-edit/{event}', [AdminEventController::class,'edit'])->middleware('admin');

    Route::get('/galery-all', [AdminGaleryController::class,'all'])->middleware('auth');
    Route::post('/galery', [AdminGaleryController::class,'store'])->middleware('auth');
    Route::delete('/galery/{galery}', [AdminGaleryController::class,'destroy'])->middleware('auth');
    Route::get('/galery-edit/{galery}', [AdminGaleryController::class,'showedit'])->middleware('auth');
    Route::post('/galery-edit/{galery}', [AdminGaleryController::class,'edit'])->middleware('auth');

    Route::get('/myclient-all', [AdminMyclientController::class,'all'])->middleware('admin');
    Route::post('/myclient', [AdminMyclientController::class,'store'])->middleware('admin');
    Route::delete('/myclient/{myclient}', [AdminMyclientController::class,'destroy'])->middleware('admin');
    Route::get('/myclient-edit/{myclient}', [AdminMyclientController::class,'showedit'])->middleware('admin');
    Route::post('/myclient-edit/{myclient}', [AdminMyclientController::class,'edit'])->middleware('admin');

    Route::get('/user-all', [AdminUserController::class,'all'])->middleware('admin');
    Route::post('/user', [AdminUserController::class,'store'])->middleware('admin');
    Route::delete('/user/{user}', [AdminUserController::class,'destroy'])->middleware('admin');
    Route::get('/user-edit/{user}', [AdminUserController::class,'showedit'])->middleware('admin');
    Route::post('/user-edit/{user}', [AdminUserController::class,'edit'])->middleware('admin');

    Route::get('/dashboard', function () {
        return view('admin.layouts.main',[
            'profile'  => Profile::first()
        ]);
    })->middleware('auth');

});

Route::get('/not-found', function (Request $request) {
    $previousUrl = $request->query('from', '/');

    return response()->view('errors.404', [
        'previousUrl' => $previousUrl
    ], 404);
});


// Route::fallback( function () {
//     return response()->view('errors.404', [], 404);
// });

Route::get('/', function () {
    return view('main',[
        'profile'  => Profile::first(),
        'categories' => Category::all(),
        'newevents' => Event::latest()->take(1)->get(),
    ]);
})->where('any', '[\/\w\.-]*');

Route::get('/web/{any?}', function () {
    return view('main',[
        'profile'  => Profile::first(),
        'categories' => Category::all(),
        'newevents' => Event::latest()->take(1)->get(),
    ]);
})->where('any', '[\/\w\.-]*');

Route::get('/admin/{any?}', function () {
    return view('admin.layouts.main',[
        'profile'  => Profile::first()
    ]);
})->where('any', '[\/\w\.-]*')->middleware('admin');

Route::get('/auth/{any?}', function () {
    return view('auth.auth',[
        'profile'  => Profile::first()
    ]);
})->where('any', '[\/\w\.-]*')->middleware('guest');



