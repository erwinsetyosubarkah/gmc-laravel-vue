<?php

namespace App\Repositories;

use App\Models\{Profile};

use App\Repositories\Contracts\KontakkamiRepositoryInterface;

/**
 * Summary of KontakkamiRepository
 */
class KontakkamiRepository implements KontakkamiRepositoryInterface
{

    /**
     * Summary of getProfile
     * @return array{page_title: string, profile: Profile|\stdClass|null}
     */
    public function getProfile()
    {

        $profile = Profile::first();

        return [
            'page_title' => 'Kontak Kami',
            'profile' => $profile
        ];
    }

}
