<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\HomeRepositoryInterface;

class HomeController extends Controller
{

    /**
     * Summary of homeRepository
     * @var object
     */
    private Object $homeRepository;

    /**
     * Summary of __construct
     * @param HomeRepositoryInterface $homeRepository
     */
    public function __construct(HomeRepositoryInterface $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function index() {
        $result = $this->homeRepository->home();
        echo json_encode($result);
    }
}
