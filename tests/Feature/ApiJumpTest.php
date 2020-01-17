<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiJumpTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUrls()
    {
        // 加密
        $key = config('app.encrypt_key');
        $token = openssl_encrypt(time(), 'AES-256-ECB', $key, 0);

        $url = 'http://'.config('app.api_domain');
        $this->json('POST', "$url/jump_urls", [
            'token'   => $token,
            'domain'  => 'aaa1.com',
            'amount ' => 10,
        ])->assertJson([
            'success' => true,
        ]);
    }
}
