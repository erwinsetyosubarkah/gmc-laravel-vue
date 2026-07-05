<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\VisidanmisiRepositoryInterface;

class VisidanmisiController extends Controller
{

    private object $visidanmisiRepository;

    public function __construct(VisidanmisiRepositoryInterface $visidanmisiRepository)
    {
        $this->visidanmisiRepository = $visidanmisiRepository;
    }

    public function index() {
        $result = $this->visidanmisiRepository->getVisidanmisi();

        echo json_encode($result);
    }
}
