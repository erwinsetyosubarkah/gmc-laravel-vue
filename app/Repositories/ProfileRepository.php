<?php

namespace App\Repositories;

use App\Models\{Profile};

use App\Repositories\Contracts\ProfileRepositoryInterface;

/**
 * Summary of ProfileRepository
 */
class ProfileRepository implements ProfileRepositoryInterface
{

    /**
     * Summary of getProfile
     * @return array{page_title: string, profile: Profile|\stdClass|null}
     */
    public function getProfile()
    {

        $profile = Profile::first();

        return [
            'page_title' => 'Profile',
            'profile' => $profile
        ];
    }

}
