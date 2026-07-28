<?php

namespace App\Repositories\Contracts;

/**
 * Summary of VisidanmisiRepositoryInterface
 */
interface VisidanmisiRepositoryInterface
{

    /**
     * Summary of getVisidanmisi
     * @return void
     */
    public function getVisidanmisi();

    /**
     * Summary of edit
     * @param array $data
     * @param object $obj
     * @param object $request
     * @return array
     */
    public function edit(array $data, object $obj, object $request);

}
