<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends ApiBaseController
{

    public function __construct(Request $request)
    {
         parent::__construct($request);
    }

    /**
     * Login user and return tocken
     *
     * {@inheritdoc}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid data',
                'error_data' => $validator->errors()->toArray()
            ], 401);
        } else {
            $credentials = request(['user_name', 'password']);
            $attemptWithEmail = Auth::attempt(['email' => $credentials['user_name'], 'password' => $credentials['password']]);
            if ($attemptWithEmail) {
                $user = Auth::user();
                $token = $user->createToken('MyApp')->accessToken;
                return response()->json([
                    'token' => $token
                ], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }
    }

}
