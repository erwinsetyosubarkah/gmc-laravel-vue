<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Models\User;
use App\Repositories\Contracts\Admin\AdminUserRepositoryInterface;

class AdminUserController extends Controller
{
    private AdminUserRepositoryInterface $adminUserRepository;

    public function __construct(AdminUserRepositoryInterface $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function all()
    {
        $result = $this->adminUserRepository->all();

        return response()->json($result);
    }

    public function store(AdminUserRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminUserRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(User $user)
    {
        $result = $this->adminUserRepository->destroy($user);

        return response()->json($result);
    }

    public function showedit(User $user)
    {
        $result = $this->adminUserRepository->showEdit($user);

        return response()->json($result);
    }

    public function edit(User $user, AdminUserRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminUserRepository->edit($validatedData, $user, $request);

        return response()->json($result);
    }
}
