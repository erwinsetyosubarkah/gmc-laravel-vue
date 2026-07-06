<?php

namespace App\Repositories\Contracts;

interface KlienkamiRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return void
     */
    public function latest(array $data);

}
