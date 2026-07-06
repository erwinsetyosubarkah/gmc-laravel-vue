<?php

namespace App\Repositories\Contracts;

interface EventRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return void
     */
    public function latest(array $data);

    /**
     * Summary of show
     * @param array $data
     * @return void
     */
    public function show(array $data);
}
