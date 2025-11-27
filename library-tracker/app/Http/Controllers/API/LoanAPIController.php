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

    public function putExtend(Request $request, int $id)
    {
        $loan = Loan::find($id);
        if (!$loan) {
            throw new Exception('Could not find loan.');
        }

        // Check if already overdue
        if (Carbon::parse($loan->due_at)->isPast()) {
            return response()->json(['message' => 'Loan is already overdue and cannot be extended'], 422);
        }

        // Validate additional_days
        $validated = $request->validate([
            'additional_days' => 'required|integer|min:1|max:14'
        ]);

        $additionalDays = $validated['additional_days'];

        // NEW due date = current due_at + additional days
        $newDueAt = Carbon::parse($loan->due_at)->addDays($additionalDays);

        // Update loan
        $loan->update([
            'due_at' => $newDueAt
        ]);

        return response()->json($loan);
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
