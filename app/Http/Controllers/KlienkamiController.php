<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlienkamiIndexRequest;
use App\Repositories\Contracts\KlienkamiRepositoryInterface;

class KlienkamiController extends Controller
{

    private Object $klienkamiRepository;

    public function __construct(KlienkamiRepositoryInterface $klienkamiRepository)
    {
        $this->klienkamiRepository = $klienkamiRepository;
    }

    public function index(KlienkamiIndexRequest $request) {

        $validatedData = $request->validated();
        $result = $this->klienkamiRepository->latest($validatedData);

        echo json_encode($result);
    }

}
