<?php

namespace App\Http\Controllers\Admin;

use App\Models\Galery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminGaleryRequest;
use App\Repositories\Contracts\Admin\AdminGaleryRepositoryInterface;

class AdminGaleryController extends Controller
{
    private AdminGaleryRepositoryInterface $adminGaleryRepository;

    public function __construct(AdminGaleryRepositoryInterface $adminGaleryRepository)
    {
        $this->adminGaleryRepository = $adminGaleryRepository;
    }

    public function all()
    {
        $result = $this->adminGaleryRepository->all();

        return response()->json($result);
    }

    public function store(AdminGaleryRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminGaleryRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(Galery $galery)
    {
        $result = $this->adminGaleryRepository->destroy($galery);

        return response()->json($result);
    }

    public function showedit(Galery $galery)
    {
        $result = $this->adminGaleryRepository->showEdit($galery);

        return response()->json($result);
    }

    public function edit(Galery $galery, AdminGaleryRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminGaleryRepository->edit($validatedData, $galery, $request);

        return response()->json($result);
    }
}
