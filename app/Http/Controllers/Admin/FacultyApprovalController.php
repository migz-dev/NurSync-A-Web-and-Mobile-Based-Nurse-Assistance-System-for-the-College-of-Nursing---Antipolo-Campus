<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FacultyApprovalController extends Controller
{
    public function index()
    {
        $pending = DB::table('faculty')->where('status','pending')->orderByDesc('created_at')->get();
        return view('admin.faculty-approvals', compact('pending'));
    }

    public function approve($id)
    {
        DB::table('faculty')->where('id',$id)->update(['status'=>'approved','updated_at'=>now()]);
        return back()->with('success','Faculty approved.');
    }

    public function reject($id)
    {
        DB::table('faculty')->where('id',$id)->update(['status'=>'rejected','updated_at'=>now()]);
        return back()->with('success','Faculty rejected.');
    }
}
