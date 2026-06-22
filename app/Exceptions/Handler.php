<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (HttpExceptionInterface $e, Request $request): ?Response {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            $message = "Tu sesi\u{00f3}n expir\u{00f3}. Inicia sesi\u{00f3}n nuevamente.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 419, [], JSON_UNESCAPED_UNICODE);
            }

            $loginRoute = $request->is('portal', 'portal/*') ? 'portal.login' : 'login';

            return redirect()->route($loginRoute)->withErrors(['sesion' => $message]);
        });
    }
}
