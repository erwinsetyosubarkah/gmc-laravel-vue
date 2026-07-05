<?php

namespace App\Repositories\Contracts;

interface ProdukkamiRepositoryInterface
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
