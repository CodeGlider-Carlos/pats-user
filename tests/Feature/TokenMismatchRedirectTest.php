<?php

namespace Tests\Feature;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TokenMismatchRedirectTest extends TestCase
{
    private const MESSAGE = 'Tu sesión expiró. Inicia sesión nuevamente.';

    public function test_web_token_mismatch_redirects_to_login_with_message(): void
    {
        Route::middleware('web')->get('/_token-mismatch', function (): void {
            throw new TokenMismatchException;
        });

        $response = $this->get('/_token-mismatch');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['sesion' => self::MESSAGE]);
    }

    public function test_portal_token_mismatch_redirects_to_portal_login(): void
    {
        Route::middleware('web')->get('/portal/_token-mismatch', function (): void {
            throw new TokenMismatchException;
        });

        $response = $this->get('/portal/_token-mismatch');

        $response->assertRedirect(route('portal.login'));
        $response->assertSessionHasErrors(['sesion' => self::MESSAGE]);
    }

    public function test_json_token_mismatch_returns_419(): void
    {
        Route::middleware('web')->get('/_token-mismatch-json', function (): void {
            throw new TokenMismatchException;
        });

        $response = $this->getJson('/_token-mismatch-json');

        $response->assertStatus(419);
        $response->assertExactJson(['message' => self::MESSAGE]);
    }
}
