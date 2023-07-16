<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Requests\DepositCreateRequest;
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
        $transactions = $this->transactionRepository->getTransactions($request->all());
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
            return $this->sendError('No transaction Found', $transactions->toArray()); 
        }
    }

    public function depositStore(DepositCreateRequest $request)
    {
         $transaction = $this->transactionRepository->createDeposit($request->all());
         
         return $this->sendResponse($transaction->toArray(), 'Deposit created successfully');
    }
}
