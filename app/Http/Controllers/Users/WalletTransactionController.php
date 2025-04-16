<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Users\WalletTransactionPermission;
use App\Http\Resources\Users\WalletTransactionResource;
use App\Http\Services\Services\WalletTransactionService;
use App\Models\Users\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{

    protected $transactionService;

    public function __construct(WalletTransactionService $walletService)
    {
        $this->transactionService = $walletService;
    }

    public function index()
    {
        $transactions = $this->transactionService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => WalletTransactionResource::collection($transactions->items()),
            'meta' => ResponseService::meta($transactions),
        ]);
    }

    public function show($id)
    {
        $transaction = $this->transactionService->show($id);

        WalletTransactionPermission::canShow($transaction);

        return response()->json([
            'success' => true,
            'data' => new WalletTransactionResource($transaction),
        ]);
    }

    // public function create(CreateRequest $request)
    // {
    //     $data = WalletTransactionPermission::create($request->validated());

    //     $transaction = $this->transactionService->create($data);

    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.wallet_transaction.item_created_successfully'),
    //         'data' => new WalletTransactionResource($transaction),
    //     ]);
    // }

    // public function update($id, UpdateRequest $request)
    // {
    //     $transaction = $this->transactionService->show($id);

    //     WalletTransactionPermission::canUpdate($transaction, $request->validated());

    //     $transaction = $this->transactionService->update($transaction, $request->validated());

    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.wallet_transaction.item_updated_successfully'),
    //         'data' => new WalletTransactionResource($transaction),
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $transaction = $this->transactionService->show($id);

    //     WalletTransactionPermission::canDelete($transaction);

    //     $deleted = $this->transactionService->destroy($transaction);

    //     return response()->json([
    //         'success' => $deleted,
    //         'message' => $deleted
    //             ? trans('messages.wallet_transaction.item_deleted_successfully')
    //             : trans('messages.wallet_transaction.failed_delete_item'),
    //     ]);
    // }

    public function createPaymentIntent(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $user = User::auth();
        $paymentData = $this->transactionService->createPaymentIntent($user, $request->amount);

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $paymentData['transaction_id'],
                'client_secret' => $paymentData['client_secret'],
            ],
        ]);
    }
}
