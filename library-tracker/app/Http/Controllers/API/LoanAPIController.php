<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class LoanAPIController extends Controller
{
    public function getIndex(?int $id = null)
    {
        if ($id) {
            return Loan::with('book', 'user')->find($id);
        }

        return Loan::with('book', 'user')->orderBy('id', 'DESC')->get();
    }

    public function postIndex(Request $request) {
        $data = $request->validate([
            'book_id'     => 'required|exists:books,id',
            'user_id'     => 'required|exists:users,id',
            'returned_at' => 'nullable|datetime',
        ]);

        return Loan::create([
            'book_id'     => $data['book_id'],
            'user_id'     => $data['user_id'],
            'loaned_at'   => now(),
            'returned_at' => $data['returned_at'] ?? null,
        ]);
    }

    public function putIndex(Request $request, int $id) {
        $loan = Loan::find($id);
        if (empty($loan)) {
            throw new Exception('Could not find loan.');
        }

        $data = $request->validate([
            'returned_at' => 'nullable|datetime',
        ]);

        $loan->update($data);
        return $loan;
    }

    public function putExtend(Request $request, int $id) {

        $loan = Loan::find($id);
        if (empty($loan)) {
            throw new Exception('Could not find loan.');
        }

        $additional_days = $request->additional_days;
        if ($additional_days > 0 && $additional_days < 14) {
            $due_at = Carbon::instance($loan['loaned_at'])->addDays($additional_days);
        }

        $data = $request->validate([
            'returned_at' => 'nullable|datetime',
        ]);

        $data['due_at'] = $due_at;

        $loan->update($data);
        return $loan;
    }

    public function deleteIndex(int $id) {
        $loan = Loan::find($id);
        if (empty($loan)) {
            throw new Exception('Could not find loan.');
        }

        $loan->delete();

        return response()->noContent();
    }
}
