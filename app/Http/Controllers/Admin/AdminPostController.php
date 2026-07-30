<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPostEditRequest;
use App\Http\Requests\Admin\AdminPostStoreRequest;
use App\Repositories\Contracts\Admin\AdminPostRepositoryInterface;

/**
 * Summary of AdminPostController
 */
class AdminPostController extends Controller
{

    /**
     * Summary of adminPostRepository
     * @var object
     */
    private Object $adminPostRepository;

    /**
     * Summary of __construct
     * @param AdminPostRepositoryInterface $adminPostRepository
     */
    public function __construct(AdminPostRepositoryInterface $adminPostRepository)
    {
        $this->adminPostRepository = $adminPostRepository;
    }

    /**
     * Summary of all
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $result = $this->adminPostRepository->all();

        return response()->json($result);
    }

    /**
     * Summary of store
     * @param AdminPostStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdminPostStoreRequest $request)
    {
        $validatedData = $request->validated();
        $result = $this->adminPostRepository->store($validatedData, $request);

        return response()->json($result);
    }

    /**
     * Summary of destroy
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $this->adminPostRepository->destroy($post);

        return response()->json([
            'status' => 'success',
            'message' => 'Data artikel berhasil dihapus.'
        ]);
    }

    /**
     * Summary of showedit
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function showedit(Post $post)
    {
        $result = $this->adminPostRepository->showEdit($post);

        return response()->json($result);
    }

    /**
     * Summary of edit
     * @param Post $post
     * @param AdminPostEditRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Post $post, AdminPostEditRequest $request)
    {
        $validatedData = $request->validated();
        $this->adminPostRepository->edit($validatedData, $post, $request);

        return response()->json([
            'status' => 'success',
            'message' => 'Data artikel berhasil diubah.'
        ]);
    }
}
