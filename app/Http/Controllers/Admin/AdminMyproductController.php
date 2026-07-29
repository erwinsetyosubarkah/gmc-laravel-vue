<?php

namespace App\Http\Controllers\Admin;

use App\Models\Myproduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminMyproductRequest;
use App\Repositories\Contracts\Admin\AdminMyproductRepositoryInterface;

class AdminMyproductController extends Controller
{
    private AdminMyproductRepositoryInterface $adminMyproductRepository;

    public function __construct(AdminMyproductRepositoryInterface $adminMyproductRepository)
    {
        $this->adminMyproductRepository = $adminMyproductRepository;
    }

    public function index()
    {
        $result = $this->adminMyproductRepository->all();

        return response()->json($result);
    }

    public function store(AdminMyproductRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminMyproductRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(Myproduct $myproduct)
    {
        $result = $this->adminMyproductRepository->destroy($myproduct);

        return response()->json($result);
    }

    public function showedit(Myproduct $myproduct)
    {
        $result = $this->adminMyproductRepository->showEdit($myproduct);

        return response()->json($result);
    }

    public function edit(Myproduct $myproduct, AdminMyproductRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminMyproductRepository->edit($validatedData, $myproduct, $request);

        return response()->json($result);
    }
}
