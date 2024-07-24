<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseRequestController extends Controller
{
    function list()
    {
        $pageTitle = 'Course Request List';
        $CoursePurchases   = CoursePurchase::where('status', 'PENDING')->with('course', 'user')->paginate(getPaginate());
        return view('admin.course_request.index', compact('pageTitle', 'CoursePurchases'));
    }

    public function status(Request $request)
    {
        try {
            $id = $request->id;
            $st = $request->st;
            $cp = CoursePurchase::findOrFail($id);
            if ($cp) {
                if ($st == 1) {
                    $cp->status = CoursePurchase::APPROVED;
                    $cp->save();
                    return response()->json(['success' => true, 'message' => 'purchase approved successfully'], 202);
                } else if ($st == 2) {
                    $cp->status = CoursePurchase::REJECTED;
                    $cp->save();
                    return response()->json(['success' => true, 'message' => 'purchase rejected successfully'], 203);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update purchase'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error'], 500);
        }
    }
}
