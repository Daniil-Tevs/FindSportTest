<?php

    namespace App\Http\Middleware\Api;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    use App\Models\User;

    class Authorization
    {
        /**
         * Handle an incoming request.
         *
         * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            $apiToken = $request->bearerToken();

            if (empty($apiToken) || !is_string($apiToken) || strlen($apiToken) !== 20)
                return response()->json(['error' => 'API token is required'], 401);

            $user = User::where('api_token', $apiToken)->select('id', 'name')->first();

            if(empty($user))
                return response()->json(['error' => 'API token is wrong'], 401);

            $request->merge(['user' => $user]);

            return $next($request);
        }
    }
