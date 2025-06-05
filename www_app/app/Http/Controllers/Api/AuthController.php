<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Verifiable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API for working with Authentication"
 * )
 * @OA\Schema(
 *     schema="Auth",
 *     title="Authentication",
 *     description="Authentication",
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password-567")
 * )
 */
class AuthController extends Controller
{
    use Verifiable;

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     description="Creates a new user and returns the data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", format="phone", example="+380667147444"),
     *             @OA\Property(property="password", type="string", format="password", example="password-567"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password-567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="users", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'sometimes|string|max:16',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'language_code' => App::getLocale(),
            'name' => $request->json('name'),
            'email' => $request->json('email'),
            'phone' => preg_replace('/[^0-9+]/', '', $request->json('phone')),
            'password' => Hash::make($request->json('password')),
        ]);

        // Generate a temporary signed URL (valid for 60 minutes)
        $verifyUrl = URL::temporarySignedRoute(
            'email.verify', // path name
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'email' => $user->email]
        );

        Mail::to($user->email)->send(new ConfirmMail($user->name, $verifyUrl));

        return response()->json(['users' => $user], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     description="Authenticate user and return a JWT token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password-567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->json('email'))->first();

        $messages = App::getMessages([
            'invalid_credentials',
            'verify_email',
            //'account_blocked',
            'new_email_sent',
        ]);

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->json('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [$messages['invalid_credentials']],
            ]);
        }

        // Check if the email is verified
        if (is_null($user->email_verified_at)) {
            // Send an email with a new link
            $verifyUrl = $this->generateVerifyUrl($user);
            Mail::to($user->email)->send(new ConfirmMail($user->name, $verifyUrl));

            return response()->json(['message' => $messages['verify_email']. ' ' . $messages['new_email_sent']], 403);
        }

        // // Check if the user is blocked
        // if ($user->is_blocked) {
        //     return response()->json(['message' => $messages['account_blocked'], 403);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'token_type' => 'Bearer'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="User logout",
     *     description="User logout.",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $messages = App::getMessages([
            'you_not_authorized',
            'you_logged_out',
        ]);
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => $messages['you_not_authorized']], 400);
        }
        $user->tokens()->delete();
        return response()->json(['message' => $messages['you_logged_out']], 200);
    }

}
