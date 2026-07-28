<?php

namespace App\Http\Controllers\Admin;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProfileEditRequest;
use App\Repositories\Contracts\Admin\AdminProfileRepositoryInterface;

class AdminProfileController extends Controller
{
    private AdminProfileRepositoryInterface $adminProfileRepository;

    public function __construct(AdminProfileRepositoryInterface $adminProfileRepository)
    {
        $this->adminProfileRepository = $adminProfileRepository;
    }

    public function index() {
        return view('admin/profile',[
            'page_title' => 'Profile',
            'profile' => Profile::find(1)
        ]);
    }


    public function edit(AdminProfileEditRequest $request) {
        $validatedData = $request->validated();
        $result = $this->adminProfileRepository->edit($validatedData, new Profile(), $request);

        return response()->json($result);
    }
}
