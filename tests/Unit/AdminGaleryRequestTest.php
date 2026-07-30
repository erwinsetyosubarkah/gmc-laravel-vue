<?php

namespace Tests\Unit;

use App\Http\Requests\Admin\AdminGaleryRequest;
use Tests\TestCase;

class AdminGaleryRequestTest extends TestCase
{
    public function test_rules_for_galery_request(): void
    {
        $request = new AdminGaleryRequest();

        $this->assertSame([
            'image_title' => 'required|min:3',
            'galery_image' => 'nullable|image|file|max:2048',
        ], $request->rules());
    }
}
