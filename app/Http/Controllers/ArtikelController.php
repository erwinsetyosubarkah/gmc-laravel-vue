<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtikelIndexRequest;
use App\Repositories\Contracts\ArtikelRepositoryInterface;
use Illuminate\Http\Request;

/**
 * Summary of ArtikelController
 */
class ArtikelController extends Controller
{

    private Object $artikelRepository;

    /**
     * Summary of __construct
     * @param ArtikelRepositoryInterface $artikelRepository
     */
    public function __construct(ArtikelRepositoryInterface $artikelRepository)
    {
        $this->artikelRepository = $artikelRepository;
    }

    /**
     * Summary of index
     * @param ArtikelIndexRequest $request
     * @return void
     */
    public function index(ArtikelIndexRequest $request) {

        $validatedData = $request->validated();
        $result = $this->artikelRepository->latest($validatedData);

        echo json_encode($result);
    }

    /**
     * Summary of show
     * @param Request $request
     * @return void
     */
    public function show(Request $request) {
        $singleArtikel = $this->artikelRepository->show(["id" => $request->id]);
        echo json_encode($singleArtikel);
    }
}
