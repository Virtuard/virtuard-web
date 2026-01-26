<?php

namespace Modules\Api\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str as IlluminateStr;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Resources\UserResource;
use Validator;

class GoogleLoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/google/login",
     *     tags={"Auth"},
     *     summary="Google Login",
     *     description="Authenticate user using Google ID token. Verifies the token with Google and creates/authenticates user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_token"},
     *             @OA\Property(property="id_token", type="string", example="eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...", description="Google ID token from client")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxxx"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", nullable=true),
     *                 @OA\Property(property="business_name", type="string", nullable=true),
     *                 @OA\Property(property="user_name", type="string", example="johndoe"),
     *                 @OA\Property(property="photo_profile", type="string", example="https://...")
     *             ),
     *             @OA\Property(property="message", type="string", example="Login success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or missing email",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="ID token is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid Google token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Invalid Google Token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account blocked",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Your account has been blocked")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Google Client ID not configured")
     *         )
     *     )
     * )
     */
    public function googleLogin(Request $request)
    {
        try {
            // 1. Validate request
            $validator = Validator::make($request->all(), [
                'id_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError(__('ID token is required'), ['errors' => $validator->errors()])->setStatusCode(400);
            }

            $token = $request->input('id_token');

            // 2. Get Google Client ID from settings
            $clientId = setting_item('google_client_id');

            if (!$clientId) {
                return $this->sendError(__('Google Client ID not configured'))->setStatusCode(500);
            }

            // 3. Verify the token with Google using HTTP request
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $token
            ]);

            if (!$response->successful()) {
                return $this->sendError(__('Invalid Google Token'))->setStatusCode(401);
            }

            $payload = $response->json();

            // 4. Verify the client ID matches
            if (!isset($payload['aud']) || $payload['aud'] !== $clientId) {
                return $this->sendError(__('Token client ID mismatch'))->setStatusCode(401);
            }

            // 5. Extract user data from payload
            $googleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? ($payload['given_name'] ?? 'User');
            $givenName = $payload['given_name'] ?? null;
            $familyName = $payload['family_name'] ?? null;
            $picture = $payload['picture'] ?? null;

            if (!$email) {
                return $this->sendError(__('Email not provided by Google'))->setStatusCode(400);
            }

            if (!$googleId) {
                return $this->sendError(__('Invalid Google token payload'))->setStatusCode(401);
            }

            // 6. Find or create user
            $provider = 'google';
            $user = User::getUserBySocialId($provider, $googleId);

            if (!$user) {
                // Check if user exists by email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Link Google account to existing user
                    $user->addMeta('social_' . $provider . '_id', $googleId);
                    $user->addMeta('social_' . $provider . '_email', $email);
                    $user->addMeta('social_' . $provider . '_name', $name);
                    if ($picture) {
                        $user->addMeta('social_' . $provider . '_avatar', $picture);
                        $user->addMeta('social_meta_avatar', $picture);
                    }
                    $user->need_update_pw = 0;
                    $user->last_login_at = now();
                    $user->save();
                } else {
                    // Create new user
                    $splitName = explode(' ', $name, 2);
                    $firstName = $splitName[0] ?? $givenName ?? $name;
                    $lastName = $splitName[1] ?? $familyName ?? null;

                    $user = new User();
                    $user->email = $email;
                    $user->password = Hash::make(IlluminateStr::random(16));
                    $user->name = $name;
                    $user->first_name = $firstName;
                    $user->last_name = $lastName;
                    $user->user_name = generate_user_name($firstName, $lastName);
                    $user->status = 'publish';
                    $user->last_login_at = Carbon::now();
                    $user->email_verified_at = Carbon::now();
                    $user->need_update_pw = 0;
                    $user->save();

                    // Add Google metadata
                    $user->addMeta('social_' . $provider . '_id', $googleId);
                    $user->addMeta('social_' . $provider . '_email', $email);
                    $user->addMeta('social_' . $provider . '_name', $name);
                    if ($picture) {
                        $user->addMeta('social_' . $provider . '_avatar', $picture);
                        $user->addMeta('social_meta_avatar', $picture);
                    }

                    // Assign default role
                    $user->assignRole(setting_item('user_role'));

                    // Send registration email
                    try {
                        event(new SendMailUserRegistered($user));
                    } catch (\Exception $exception) {
                        Log::warning("SendMailUserRegistered: " . $exception->getMessage());
                    }
                }
            } else {
                // Update last login
                $user->last_login_at = now();
                $user->need_update_pw = 0;
                $user->save();
            }

            // 7. Check if user is blocked
            if ($user->deleted == 1 || in_array($user->status, ['blocked'])) {
                return $this->sendError(__('Your account has been blocked'))->setStatusCode(403);
            }

            // 8. Create access token (Sanctum)
            $tokenResult = $user->createToken('Personal Access Token');
            $accessToken = $tokenResult->plainTextToken;

            // 9. Return response in the format expected by the app
            // Using same format as AuthController login method
            return [
                'token' => $accessToken,
                'user' => new UserResource($user),
                'status' => 1,
                'message' => __('Login success')
            ];

        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage())->setStatusCode(500);
        }
    }
}
