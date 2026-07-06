<?php

namespace App\Repositories;

use App\Models\{Event, Profile};

use App\Repositories\Contracts\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return array{events: \Illuminate\Pagination\LengthAwarePaginator<int, Event>, page_title: string}
     */
    public function latest(array $data)
    {
        $events = Event::latest();

        if(isset($data['search'])){
            $events->where('event_title','like', '%' . $data['search'] . '%');
        }

        $events = $events->paginate(6);

        return [
            'page_title' => 'Event',
            'events' => $events
        ];
    }

    /**
     * Summary of show
     * @param array $data
     * @return array{event: Event|\Illuminate\Database\Eloquent\Collection<int, Event>|\stdClass|null, page_title: string, profile: Profile|\stdClass|null}
     */
    public function show(array $data)
    {

        $profile = Profile::first();
        $event = Event::find($data['id']);

        return [
            'page_title' => 'Event',
            'profile' => $profile,
            'event' => $event
        ];
    }
}
