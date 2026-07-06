<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventIndexRequest;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{

    private Object $eventRepository;

    /**
     * Summary of __construct
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Summary of index
     * @param EventIndexRequest $request
     * @return void
     */
    public function index(EventIndexRequest $request) {

        $validatedData = $request->validated();
        $result = $this->eventRepository->latest($validatedData);

        echo json_encode($result);
    }

    /**
     * Summary of show
     * @param Request $request
     * @return void
     */
    public function show(Request $request) {
        $result = $this->eventRepository->show(["id" => $request->id]);
        echo json_encode($result);
    }
}
