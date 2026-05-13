<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchSwitcherController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $branchId = $request->input('branch_id');

        if (empty($branchId)) {
            $request->session()->forget('current_branch_id');
        } else {
            $branch = Branch::query()->find($branchId);
            if ($branch) {
                $user = $request->user();
                if ($user && ! $user->isSuperAdmin() && $user->company_id && $user->company_id !== $branch->company_id) {
                    abort(403, 'You cannot switch to a branch outside your company.');
                }
                $request->session()->put('current_branch_id', $branch->id);
            }
        }

        return back();
    }
}
