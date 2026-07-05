<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\KontakkamiRepositoryInterface;

/**
 * Summary of KontakkamiController
 */
class KontakkamiController extends Controller
{

    private Object $kontakkamiRepository;

    public function __construct(KontakkamiRepositoryInterface $kontakkamiRepository)
    {
        $this->kontakkamiRepository = $kontakkamiRepository;
    }

    public function index() {

        $result = $this->kontakkamiRepository->getProfile();

        echo json_encode($result);

    }
}
