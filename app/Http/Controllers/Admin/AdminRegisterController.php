<?php

namespace App\Http\Controllers\Admin;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRegisterRequest;
use App\Repositories\Contracts\Admin\AdminRegisterRepositoryInterface;

class AdminRegisterController extends Controller
{
    private AdminRegisterRepositoryInterface $registerRepository;

    public function __construct(AdminRegisterRepositoryInterface $registerRepository)
    {
        $this->registerRepository = $registerRepository;
    }
    public function index() {
        return view('admin/register',[
            'site_profile' => Profile::first()
        ]);
    }

    public function store(AdminRegisterRequest $request) {
        $validatedData = $request->validated();
        $result = $this->registerRepository->store($validatedData, $request);

        return response()->json($result, $result['status'] === 'success' ? 200 : 422);
    }
}
