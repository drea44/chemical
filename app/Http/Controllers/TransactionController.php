<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['chemical', 'performer', 'location']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('chemical', fn($c) => $c->where('chemical_name', 'like', "%{$search}%")
                      ->orWhere('chemical_code', 'like', "%{$search}%"));
            });
        }

        if ($type = $request->get('type')) {
            $query->where('transaction_type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($userId = $request->get('user')) {
            $query->where('performed_by', $userId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        if ($chemicalId = $request->get('chemical')) {
            $query->where('chemical_id', $chemicalId);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(25)->withQueryString();
        $users        = User::orderBy('name')->get();
        $chemicals    = Chemical::orderBy('chemical_name')->get(['id', 'chemical_name', 'chemical_code']);

        return view('transactions.index', compact('transactions', 'users', 'chemicals'));
    }
}
