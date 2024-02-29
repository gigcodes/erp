<?php

namespace App\Http\Controllers\Api\v1\Auth;

use Auth;
use App\User;
use Exception;
use Illuminate\Http\Response;
use App\Models\BearerAccessTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @param privateBearerAccessTokens $bearerToken
     *
     * @return void
     */
    public function __construct(private BearerAccessTokens $bearerToken)
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        auth()->attempt($credentials);

        $this->bearerToken->setToken($token);
        $this->bearerToken->setUser(Auth::user());
        $this->bearerToken->save();

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function register()
    {
        $data = request()->all();

        try {
            request()->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            return response()->json([
                'message' => sprintf('User successful created. You can generate auth token.'),
                'data'    => [
                    'email' => $user->email,
                    'name'  => $user->name,
                ],
            ]);
        } catch (ValidationException $e) {
            return \response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e
                    ->validator
                    ->errors()
                    ->messages(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return \response()->json(['message' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
