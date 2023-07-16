<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Requests\DepositCreateRequest;
use App\Http\Requests\WithdrawalCreateRequest;
use Validator;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionController extends BaseController
{

    private $transactionRepository;
    public function __construct(TransactionRepositoryInterface $transactionRepository){
        $this->transactionRepository = $transactionRepository;
    }
    
    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransactions();
        if(count($transactions)){
            return $this->sendResponse($transactions->toArray(), 'Transactions retrived');
        }else{
            return $this->sendError('No transaction Found', $transactions->toArray()); 
        }
    }

    public function depositGet(Request $request)
    {
        $transactions = $this->transactionRepository->getDeposit();
        if(count($transactions)){
            return $this->sendResponse($transactions->toArray(), 'Deposit Transactions retrived');
        }else{
            return $this->sendError('No Deposit Transaction Found', $transactions->toArray()); 
        }
    }

    public function depositStore(DepositCreateRequest $request)
    {
         $transaction = $this->transactionRepository->createDeposit($request->all());
         
         return $this->sendResponse($transaction->toArray(), 'Deposited successfully');
    }

    public function withdrawalGet(Request $request)
    {
        $transactions = $this->transactionRepository->getWithdrawal();
        if(count($transactions)){
            return $this->sendResponse($transactions->toArray(), 'Withdrawal Transactions retrived');
        }else{
            return $this->sendError('No Withdrawal Transaction Found', $transactions->toArray()); 
        }
    }

    public function withdrawalStore(WithdrawalCreateRequest $request)
    {
         $transaction = $this->transactionRepository->createWithdrawal($request->all());
         
         return $this->sendResponse($transaction->toArray(), 'Withdrawal successfull');
    }

}
