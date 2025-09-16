<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RecruitmentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRecruitmentCreateRequest;
use App\Http\Requests\Admin\AdminRecruitmentUpdateRequest;
use App\Models\Commission;
use App\Models\Country;
use App\Models\Recruitment;
use App\Models\RecruitmentSource;
use App\Models\RecruitmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRecruitmentController extends Controller
{

    public function index(RecruitmentDataTable $dataTable)
    {
        // Check if the authenticated user's main role is 'admin'
        $user = Auth::user();
        if ($user && $user->mainRoleRelation()->where('name', 'admin')->exists()) {
            // If the main role is admin, get all commissions
            $commissions = Commission::with('user', 'payment.user', 'payment.salesPerson', 'payment.agent')
                ->get();
        } else {
            // Otherwise, get only the current user's commissions
            $commissions = Commission::where('user_id', $user->id)
                ->with('user', 'payment.user', 'payment.salesPerson', 'payment.agent')
                ->get();
        }
        
        // Sort the commissions
        $commissions = $commissions->sortByDesc(function ($commission) {
            return $commission->payment?->user?->created_at;
        });

        // Calculate total paid and unpaid amounts
        $paidAmount = $commissions->where('status', 'paid')->sum('amount');
        $unpaidAmount = $commissions->where('status', 'unpaid')->sum('amount');

        $recruitments = Recruitment::with('callLogs')->get();

        return $dataTable->render('admin.recruitment.index', compact('recruitments', 'commissions', 'paidAmount', 'unpaidAmount'));
    }

    public function create()
    {
        $recruitmentStatuses = RecruitmentStatus::where('status', 1)->get();
        $recruitmentSources = RecruitmentSource::where('status', 1)->get();
        $countries = Country::where('status', 1)->get();
        return view('admin.recruitment.create', compact('recruitmentStatuses','recruitmentSources','countries'));
    }

    public function store(AdminRecruitmentCreateRequest $request)
    {

        $recruitment = Recruitment::create([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'email'               => $request->email,
            'country_id'          => $request->country_id,
            'source_id'           => $request->source_id,
            'status_id'           => $request->status_id,
        ]);

        notyf()->success('Recruitment created successfully!');
        return redirect()->route('admin.recruitment.edit', $recruitment->id);
    }



    public function show(string $id)
    {
        //
    }

    public function edit(Recruitment $recruitment)
    {
        $recruitmentStatuses = RecruitmentStatus::where('status', 1)->get();
        $recruitmentSources = RecruitmentSource::where('status', 1)->get();
        $countries = Country::where('status', 1)->get();
        return view('admin.recruitment.edit', compact('recruitment', 'recruitmentStatuses','recruitmentSources','countries'));
    }

    public function update(AdminRecruitmentUpdateRequest $request, Recruitment $recruitment)
    {
        $recruitment->update([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'email'               => $request->email,
            'country_id'          => $request->country_id,
            'source_id'           => $request->source_id,
            'status_id'           => $request->status_id,
        ]);

        // Delete selected
        if ($request->filled('deleted_calls')) {
            $idsToDelete = explode(',', $request->deleted_calls);
            $recruitment->callLogs()->whereIn('id', $idsToDelete)->delete();
        }

        // Create/update call logs
        if ($request->has('call_logs')) {
            foreach ($request->call_logs as $log) {
                $data = [
                    'called_by' => Auth::id(),
                    'communication_method' => $log['communication_method'] ?? null,
                    'status_id' => $log['call_status_id'] ?? null,
                    'note' => $log['note'] ?? null,
                ];

                if (!empty($log['existing_id'])) {
                    $recruitment->callLogs()->where('id', $log['existing_id'])->update($data);
                } else {
                    $recruitment->callLogs()->create($data);
                }
            }
        }

        notyf()->success('Recruitment updated successfully!');

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.recruitment.index');
        }
    }

    public function destroy(string $id)
    {
        try {
            $recruitment = Recruitment::findOrFail($id);
            $recruitment->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }
    
}
