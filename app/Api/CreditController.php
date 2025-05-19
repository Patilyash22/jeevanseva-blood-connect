
<?php

namespace App\Api;

use App\Models\User;
use App\Models\Credit;
use App\Models\Setting;
use App\Services\UserCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CreditController
{
    protected $creditService;
    
    public function __construct()
    {
        $this->creditService = new UserCreditService();
    }
    
    /**
     * Get user credit balance and transaction history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCredits(Request $request, $userId = null)
    {
        // Use authenticated user if no user ID provided
        if (!$userId) {
            $userId = Auth::id();
        }
        
        // Only admins can view other users' credits
        if ($userId != Auth::id() && !Auth::user()->is_admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to view this user\'s credits'
            ], 403);
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        
        $creditSummary = Credit::getUserSummary($userId);
        
        return response()->json([
            'status' => 'success',
            'data' => $creditSummary
        ]);
    }
    
    /**
     * Redeem a promo code for credits
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function redeemPromoCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid promo code',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $promoCode = $request->input('promo_code');
        $user = Auth::user();
        
        // Check if the code is a valid promo code
        // This is a placeholder - you'll need to implement promo code validation
        // based on your database structure
        $isValid = false;
        $creditAmount = 0;
        
        // Example implementation
        $sql = "SELECT * FROM promo_codes WHERE code = '$promoCode' AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())";
        $result = DB::select($sql);
        
        if (!empty($result)) {
            $promo = $result[0];
            $isValid = true;
            $creditAmount = $promo->credit_amount;
            
            // Mark the promo code as used
            // Again, this is a placeholder implementation
            if ($promo->is_single_use) {
                DB::table('promo_codes')
                    ->where('id', $promo->id)
                    ->update(['is_active' => 0, 'used_at' => now(), 'used_by' => $user->id]);
            }
        }
        
        if ($isValid) {
            // Add credits to user
            $success = $this->creditService->processCredits(
                $user,
                $creditAmount,
                'promo_code',
                'Redeemed promo code: ' . $promoCode
            );
            
            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Promo code redeemed successfully',
                    'data' => [
                        'credits_added' => $creditAmount,
                        'new_balance' => $user->credits
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process credits'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired promo code'
            ], 400);
        }
    }
    
    /**
     * Get credit settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCreditSettings()
    {
        $settings = Setting::getCreditSettings();
        
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }
    
    /**
     * Update credit settings (admin only)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCreditSettings(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'signup_bonus' => 'sometimes|required|integer|min:0',
            'referral_bonus' => 'sometimes|required|integer|min:0',
            'donor_view_cost' => 'sometimes|required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $updated = false;
        
        foreach ($request->all() as $key => $value) {
            if (in_array($key, ['signup_bonus', 'referral_bonus', 'donor_view_cost'])) {
                Setting::set($key, $value);
                $updated = true;
            }
        }
        
        if ($updated) {
            return response()->json([
                'status' => 'success',
                'message' => 'Credit settings updated successfully',
                'data' => Setting::getCreditSettings()
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No valid settings provided'
            ], 400);
        }
    }
}
