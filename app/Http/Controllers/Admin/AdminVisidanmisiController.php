<?php

namespace App\Http\Controllers\Admin;

use App\Models\Visidanmisi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminVisidanmisiEditRequest;
use App\Repositories\Contracts\VisidanmisiRepositoryInterface;

class AdminVisidanmisiController extends Controller
{
    private VisidanmisiRepositoryInterface $visidanmisiRepository;

    public function __construct(VisidanmisiRepositoryInterface $visidanmisiRepository)
    {
        $this->visidanmisiRepository = $visidanmisiRepository;
    }

    public function index() {
        return view('admin/visidanmisi',[
            'page_title' => 'Visi dan Misi',
            'visidanmisi' => Visidanmisi::find(1)
        ]);
    }

    public function edit(AdminVisidanmisiEditRequest $request) {
        $validatedData = $request->validated();
        $result = $this->visidanmisiRepository->edit($validatedData, new Visidanmisi(), $request);

        return response()->json($result);
    }
}
