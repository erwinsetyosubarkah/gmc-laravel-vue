<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukkamiIndexRequest;
use App\Repositories\Contracts\ProdukkamiRepositoryInterface;
use Illuminate\Http\Request;

class ProdukkamiController extends Controller
{

    private object $produkkamiRepository;
    public function __construct(ProdukkamiRepositoryInterface $produkkamiRepository)
    {
        $this->produkkamiRepository = $produkkamiRepository;
    }
    public function index(ProdukkamiIndexRequest $request) {
        $validatedData = $request->validated();
        $result = $this->produkkamiRepository->latest($validatedData);

        echo json_encode($result);
    }

    public function show(Request $request) {
        $result = $this->produkkamiRepository->show(["id" => $request->id]);
        echo json_encode($result);
    }
}
