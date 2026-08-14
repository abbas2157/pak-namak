<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:cash,bank,jazzcash,easypaisa,other',
            'bank_name'      => 'required_if:type,bank|nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
        ]);

        $account = Account::create([
            'name'           => $request->name,
            'type'           => $request->type,
            'bank_name'      => $request->type === 'bank' ? $request->bank_name : null,
            'account_number' => $request->account_number,
            'is_active'      => true,
        ]);

        return response()->json(['success' => true, 'account' => $account]);
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'bank_name'      => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
        ]);

        $account->update($request->only(['name', 'bank_name', 'account_number']));

        return response()->json(['success' => true, 'account' => $account]);
    }

    /**
     * Accounts with transaction history are deactivated (hidden from new
     * payment forms) rather than deleted, so past ledger entries keep their
     * account reference intact.
     */
    public function destroy(Account $account)
    {
        if ($account->ledgerEntries()->exists()) {
            $account->update(['is_active' => false]);
            return response()->json(['success' => true, 'deactivated' => true, 'message' => 'This account has transaction history, so it was deactivated instead of deleted.']);
        }

        $account->delete();

        return response()->json(['success' => true, 'deactivated' => false]);
    }

    public function reactivate(Account $account)
    {
        $account->update(['is_active' => true]);

        return response()->json(['success' => true]);
    }
}
