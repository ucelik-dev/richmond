@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">LESSONS</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.lesson.create') }}" class="btn btn-default">
                            <i class="fa-solid fa-plus me-2"></i>
                            Add new
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TITLE</th>
                                    <th>COURSES</th>
                                    <th>MODULES</th>
                                    <th>ORDER</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lessons as $lesson)
                                    
                                    <tr>
                                        <td>{{ $lesson->id }}</td>
                                        <td>{{ $lesson->title }}</td>
                                        <td>
                                            @foreach ($lesson->modules as $module)
                                                @foreach ($module->courses as $course)
                                                    <span class="badge bg-primary text-primary-fg fw-normal mb-1">{{ $course->title }} ({{ $course->level->name }})</span>
                                                    <span class="badge badge-light text-dark fw-normal mb-1">{{ $course->awardingBody?->name }}</span>
                                                    <br>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($lesson->modules as $module)
                                                <span class="badge bg-primary text-primary-fg fw-normal mb-1">{{ $module->title }}</span>
                                                <span class="badge badge-light text-dark fw-normal mb-1">{{ $module->awardingBody?->name }}</span>
                                                <br>
                                            @endforeach
                                        </td>
                                        <td>{{ $lesson->order }}</td>

                                        <td>
                                            @if($lesson->status == 1)
                                                <span class="badge bg-green text-green-fg fw-normal">Active</span>
                                                
                                            @elseif($lesson->status == 0)
                                                <span class="badge bg-red text-red-fg fw-normal">Inactive</span>
                                            @else
                                                <span class="badge bg-yellow text-yellow-fg fw-normal">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('edit_admin_lessons')
                                                <a href="{{ route('admin.lesson.edit', $lesson->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                    <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                                </a>
                                            @endcan
                                            @can('edit_admin_lessons')
                                                <a href="{{ route('admin.lesson.destroy', $lesson->id) }}" class="text-red delete-item text-decoration-none">
                                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="5">No data available.</td>
                                    </tr>

                                @endforelse
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $lessons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
