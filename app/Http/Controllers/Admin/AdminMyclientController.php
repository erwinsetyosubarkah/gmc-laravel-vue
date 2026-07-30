<?php

namespace App\Http\Controllers\Admin;

use App\Models\Myclient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminMyclientRequest;
use App\Repositories\Contracts\Admin\AdminMyclientRepositoryInterface;

class AdminMyclientController extends Controller
{
    private AdminMyclientRepositoryInterface $adminMyclientRepository;

    public function __construct(AdminMyclientRepositoryInterface $adminMyclientRepository)
    {
        $this->adminMyclientRepository = $adminMyclientRepository;
    }

    public function all()
    {
        $result = $this->adminMyclientRepository->all();

        return response()->json($result);
    }

    public function store(AdminMyclientRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminMyclientRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(Myclient $myclient)
    {
        $result = $this->adminMyclientRepository->destroy($myclient);

        return response()->json($result);
    }

    public function showedit(Myclient $myclient)
    {
        $result = $this->adminMyclientRepository->showEdit($myclient);

        return response()->json($result);
    }

    public function edit(Myclient $myclient, AdminMyclientRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminMyclientRepository->edit($validatedData, $myclient, $request);

        return response()->json($result);
    }
}
