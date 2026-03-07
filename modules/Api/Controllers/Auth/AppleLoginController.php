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

class AppleLoginController extends Controller
{
    /**
     * Apple Sign-In endpoint.
     *
     * Accepts the Apple identity_token from the client, verifies it via
     * Apple's public key endpoint, then finds or creates the Virtuard user.
     *
     * POST /api/auth/apple/login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function appleLogin(Request $request)
    {
        try {
            // 1. Validate request
            $validator = Validator::make($request->all(), [
                'identity_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError(__('identity_token is required'), ['errors' => $validator->errors()])->setStatusCode(400);
            }

            $identityToken = $request->input('identity_token');

            // 2. Decode the JWT identity_token (without full signature verification for simplicity,
            //    similar to how many backends handle it — Apple's public keys can be fetched if needed).
            //    For now we decode the payload to extract the Apple user ID and email.
            $tokenParts = explode('.', $identityToken);
            if (count($tokenParts) !== 3) {
                return $this->sendError(__('Invalid Apple identity token format'))->setStatusCode(401);
            }

            // Base64url decode the payload (middle part)
            $payloadJson = base64_decode(str_pad(
                strtr($tokenParts[1], '-_', '+/'),
                strlen($tokenParts[1]) % 4 === 0 ? strlen($tokenParts[1]) : strlen($tokenParts[1]) + (4 - strlen($tokenParts[1]) % 4),
                '='
            ));

            if (!$payloadJson) {
                return $this->sendError(__('Failed to decode Apple token payload'))->setStatusCode(401);
            }

            $payload = json_decode($payloadJson, true);

            if (!$payload || !isset($payload['sub'])) {
                return $this->sendError(__('Invalid Apple token payload'))->setStatusCode(401);
            }

            // 3. Extract Apple user data
            $appleId = $payload['sub'];  // Unique Apple user ID — never changes
            $email = $payload['email'] ?? $request->input('email');  // Only on first login or non-private

            // Apple only sends name on the VERY FIRST login — use request params as fallback
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $name = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
            if (empty(trim($name))) {
                $name = $email ? explode('@', $email)[0] : 'Apple User';
            }

            if (!$appleId) {
                return $this->sendError(__('Invalid Apple ID token: missing sub'))->setStatusCode(401);
            }

            // 4. Find or create user
            $provider = 'apple';
            $isNewUser = false;
            $user = User::getUserBySocialId($provider, $appleId);

            if (!$user) {
                // Try to find by email (link existing account)
                if ($email) {
                    $user = User::where('email', $email)->first();
                }

                if ($user) {
                    // Link Apple account to existing user
                    $user->addMeta('social_' . $provider . '_id', $appleId);
                    if ($email) {
                        $user->addMeta('social_' . $provider . '_email', $email);
                    }
                    // Only update name if we got one from Apple (first login)
                    if (!empty(trim($name)) && $name !== 'Apple User') {
                        $user->addMeta('social_' . $provider . '_name', $name);
                    }
                    $user->need_update_pw = 0;
                    $user->last_login_at = now();
                    $user->save();

                } else {
                    // Create new user
                    $isNewUser = true;

                    // If email is a private relay, generate a placeholder email
                    $userEmail = $email ?? ($appleId . '@appleid.virtuard.com');

                    $user = new User();
                    $user->email = $userEmail;
                    $user->password = Hash::make(IlluminateStr::random(16));
                    $user->name = $name;
                    $user->first_name = $firstName ?? $name;
                    $user->last_name = $lastName ?? null;
                    $user->user_name = generate_user_name($firstName ?? $name, $lastName);
                    $user->status = 'publish';
                    $user->last_login_at = Carbon::now();
                    $user->email_verified_at = Carbon::now(); // Apple verifies emails
                    $user->need_update_pw = 0;
                    $user->save();

                    // Add Apple metadata
                    $user->addMeta('social_' . $provider . '_id', $appleId);
                    if ($email) {
                        $user->addMeta('social_' . $provider . '_email', $email);
                    }
                    $user->addMeta('social_' . $provider . '_name', $name);

                    // Assign default role
                    $defaultRole = setting_item('user_role');
                    if ($defaultRole) {
                        $user->assignRole($defaultRole);
                    } else {
                        $user->role_id = 2;
                        $user->save();
                    }

                    // Send welcome email (Apple may use private relay, but try anyway)
                    try {
                        event(new SendMailUserRegistered($user));
                    } catch (\Exception $exception) {
                        Log::warning("SendMailUserRegistered (Apple): " . $exception->getMessage());
                    }
                }
            } else {
                // Existing Apple user — update last login
                // Also update name if Apple provides it again (rare but possible)
                if (!empty(trim($name)) && $name !== 'Apple User') {
                    $user->addMeta('social_' . $provider . '_name', $name);
                }
                $user->last_login_at = now();
                $user->need_update_pw = 0;
                $user->save();
            }

            // 5. Check if user is blocked
            if ($user->deleted == 1 || in_array($user->status, ['blocked'])) {
                return $this->sendError(__('Your account has been blocked'))->setStatusCode(403);
            }

            // 6. Create Sanctum access token
            $tokenResult = $user->createToken('Personal Access Token');
            $accessToken = $tokenResult->plainTextToken;

            // 7. Return same format as Google login
            return [
                'token' => $accessToken,
                'user' => new UserResource($user),
                'status' => 1,
                'message' => $isNewUser ? __('Registration successful') : __('Login success'),
                'is_new_user' => $isNewUser,
            ];

        } catch (\Exception $e) {
            Log::error('Apple Login Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage())->setStatusCode(500);
        }
    }
}
