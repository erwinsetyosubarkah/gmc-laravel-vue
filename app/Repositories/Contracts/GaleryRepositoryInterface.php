<?php

namespace App\Repositories\Contracts;

interface GaleryRepositoryInterface
{

    /**
     * Summary of latest
     * @param array $data
     * @return void
     */
    public function latest(array $data);

}
