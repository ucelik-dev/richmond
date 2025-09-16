<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupShare;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorGroupShareController extends Controller
{
    use FileUpload;

    public function index(Group $group)
    {
        // Ensure this group belongs to the instructor
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        $shares = GroupShare::where('group_id', $group->id)->latest()->get();

        return view('frontend.instructor.group.group-shares.index', compact('shares','group'));
    }

    public function create(Group $group)
    {
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        return view('frontend.instructor.group.group-shares.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'link' => 'nullable|url|max:255',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $filePath = $this->uploadFile($request->file('file'), 'uploads/group-shares', $request->title);
        }

        GroupShare::create([
            'group_id' => $group->id,
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content ?? null,
            'link' => $request->link ?? null,
            'file' => $filePath,
        ]);

        notyf()->success('Shared successfully!');
        return redirect()->route('instructor.groups.group-shares.index', $group);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Group $group, GroupShare $groupShare)
    {
        abort_unless($groupShare->group_id === $group->id, 404);
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        return view('frontend.instructor.group.group-shares.edit', [
            'group' => $group,
            'share' => $groupShare,
        ]);
    }

    public function update(Request $request, Group $group, GroupShare $groupShare)
    {
        // Validate that the group share belongs to the group
        abort_unless($groupShare->group_id === $group->id, 404);

        // Validate that the group belongs to the instructor
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        // Validate form input
        // Validate
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'link'    => 'nullable|url',
            'file'    => 'nullable|file|max:10240',
        ]);

        // Handle file replacement
        if ($request->hasFile('file')) {
            $this->deleteFile($groupShare->file); // delete old
            $validated['file'] = $this->uploadFile(
                $request->file('file'),
                'uploads/group-shares',
                $validated['title']
            );
        }

        // Update
        $groupShare->update($validated);

        notyf()->success('Group share updated successfully!');
        return redirect()->route('instructor.groups.group-shares.index', $group->id);
    }

    public function destroy(Group $group, GroupShare $groupShare)
    {
        // Ensure the share belongs to the group
        abort_unless($groupShare->group_id === $group->id, 404);

        // Ensure the group belongs to the instructor
        abort_unless($group->instructor_id === Auth::user()->id, 403);

        try {
       
            if (!empty($groupShare->file)) {
                $this->deleteFile($groupShare->file);
            }

            $groupShare->delete();
       
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
