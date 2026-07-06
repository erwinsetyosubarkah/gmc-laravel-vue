<?php

namespace App\Http\Controllers;

use App\Http\Requests\GaleryIndexRequest;
use App\Repositories\Contracts\GaleryRepositoryInterface;

class GaleryController extends Controller
{

    private Object $galeryRepository;

    /**
     * Summary of __construct
     * @param GaleryRepositoryInterface $galeryRepository
     */
    public function __construct(GaleryRepositoryInterface $galeryRepository)
    {
        $this->galeryRepository = $galeryRepository;
    }

    /**
     * Summary of index
     * @param GaleryIndexRequest $request
     * @return void
     */
    public function index(GaleryIndexRequest $request) {

        $validatedData = $request->validated();
        $result = $this->galeryRepository->latest($validatedData);

        echo json_encode($result);
    }

}
