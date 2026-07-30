<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Repositories\{ArtikelRepository, EventRepository, GaleryRepository, HomeRepository, KlienkamiRepository, KontakkamiRepository, ProdukkamiRepository, ProfileRepository, VisidanmisiRepository};
use App\Repositories\Admin\{AdminCategoryRepository, AdminEventRepository, AdminMyproductRepository, AdminPostRepository, AdminProfileRepository, AdminRegisterRepository};
use App\Repositories\Contracts\{ArtikelRepositoryInterface, EventRepositoryInterface, GaleryRepositoryInterface, HomeRepositoryInterface, KlienkamiRepositoryInterface, KontakkamiRepositoryInterface, ProdukkamiRepositoryInterface, ProfileRepositoryInterface, VisidanmisiRepositoryInterface};
use App\Repositories\Contracts\Admin\{AdminCategoryRepositoryInterface, AdminEventRepositoryInterface, AdminMyproductRepositoryInterface, AdminPostRepositoryInterface, AdminProfileRepositoryInterface, AdminRegisterRepositoryInterface};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
        $this->app->bind(ArtikelRepositoryInterface::class, ArtikelRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(VisidanmisiRepositoryInterface::class, VisidanmisiRepository::class);
        $this->app->bind(ProdukkamiRepositoryInterface::class, ProdukkamiRepository::class);
        $this->app->bind(KontakkamiRepositoryInterface::class, KontakkamiRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(GaleryRepositoryInterface::class, GaleryRepository::class);
        $this->app->bind(KlienkamiRepositoryInterface::class, KlienkamiRepository::class);

        // Admin
        $this->app->bind(AdminPostRepositoryInterface::class, AdminPostRepository::class);
        $this->app->bind(AdminProfileRepositoryInterface::class, AdminProfileRepository::class);
        $this->app->bind(AdminMyproductRepositoryInterface::class, AdminMyproductRepository::class);
        $this->app->bind(AdminEventRepositoryInterface::class, AdminEventRepository::class);
        $this->app->bind(AdminRegisterRepositoryInterface::class, AdminRegisterRepository::class);
        $this->app->bind(AdminCategoryRepositoryInterface::class, AdminCategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Gate::define('admin', function (User $user) {
            return $user->level === 'admin';
        });
    }
}
