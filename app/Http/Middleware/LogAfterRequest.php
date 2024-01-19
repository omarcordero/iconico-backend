<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogAfterRequest {

        public function handle($request, \Closure  $next)
        {
                return $next($request);
        }

        public function terminate($request, $response)
        {
        	$logger = new Logger(10);
        	$logger->pushHandler(new StreamHandler(storage_path('logs/auth.log')), Logger::INFO);
                $logger->info('app.requests', ['request' => $request->all(), 'response' => $response]);
        }

}

?>

