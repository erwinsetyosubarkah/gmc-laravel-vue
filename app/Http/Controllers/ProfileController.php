<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ProfileRepositoryInterface;

/**
 * Summary of ProfileController
 */
class ProfileController extends Controller
{

    private Object $profileRepository;

    /**
     * Summary of __construct
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Summary of index
     * @return void
     */
    public function index() {
        $result = $this->profileRepository->getProfile();

        echo json_encode($result);
    }
}
