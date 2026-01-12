<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        if ($assignment->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $assignment->delete();

        return back()->with('success', 'Application withdrawn.');
    }
}
