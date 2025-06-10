<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Generate the next available account number based on type, group, and class.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAccountNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_code' => 'required|exists:account_types,code',
            'group_code' => 'required|exists:account_groups,code',
            'class_code' => 'required|exists:account_classes,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the last account number for the given type, group, and class
            $lastAccount = ChartOfAccount::where('type_code', $request->type_code)
                ->where('group_code', $request->group_code)
                ->where('class_code', $request->class_code)
                ->orderBy('account_no', 'desc')
                ->first();

            // Generate the next account number
            $nextNumber = $lastAccount ? (int)substr($lastAccount->account_no, -4) + 1 : 1;
            $accountNumber = sprintf(
                '%02d.%02d.%02d.%04d',
                $request->type_code,
                $request->group_code,
                $request->class_code,
                $nextNumber
            );

            return response()->json([
                'success' => true,
                'account_number' => $accountNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate account number',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 