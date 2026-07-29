<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCategoryRequest;
use App\Repositories\Contracts\Admin\AdminCategoryRepositoryInterface;

class AdminCategoryController extends Controller
{
    private AdminCategoryRepositoryInterface $adminCategoryRepository;

    public function __construct(AdminCategoryRepositoryInterface $adminCategoryRepository)
    {
        $this->adminCategoryRepository = $adminCategoryRepository;
    }

    public function all()
    {
        $result = $this->adminCategoryRepository->all();

        return response()->json($result);
    }

    public function store(AdminCategoryRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminCategoryRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(Category $category)
    {
        $result = $this->adminCategoryRepository->destroy($category);

        return response()->json($result);
    }

    public function showedit(Category $category)
    {
        $result = $this->adminCategoryRepository->showEdit($category);

        return response()->json($result);
    }

    public function edit(Category $category, AdminCategoryRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminCategoryRepository->edit($validatedData, $category, $request);

        return response()->json($result);
    }
}
