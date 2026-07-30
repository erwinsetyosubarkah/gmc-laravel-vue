<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminEventRequest;
use App\Models\Event;
use App\Repositories\Contracts\Admin\AdminEventRepositoryInterface;

class AdminEventController extends Controller
{
    private AdminEventRepositoryInterface $adminEventRepository;

    public function __construct(AdminEventRepositoryInterface $adminEventRepository)
    {
        $this->adminEventRepository = $adminEventRepository;
    }

    public function all()
    {
        $result = $this->adminEventRepository->all();

        return response()->json($result);
    }

    public function store(AdminEventRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminEventRepository->store($validatedData, $request);

        return response()->json($result);
    }

    public function destroy(Event $event)
    {
        $result = $this->adminEventRepository->destroy($event);

        return response()->json($result);
    }

    public function showedit(Event $event)
    {
        $result = $this->adminEventRepository->showEdit($event);

        return response()->json($result);
    }

    public function edit(Event $event, AdminEventRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->adminEventRepository->edit($validatedData, $event, $request);

        return response()->json($result);
    }
}
