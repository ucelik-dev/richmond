@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MODULES</h3>
                    <div class="card-actions">
                        @if(auth()->user()?->canResource('admin_modules','create'))
                            <a href="{{ route('admin.module.create') }}" class="btn btn-default">
                                <i class="fa-solid fa-plus me-2"></i>
                                Add new
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TITLE</th>
                                    <th>LEVEL</th>
                                    <th>AWARDING BODY</th>
                                    <th>COURSES</th>
                                    <th>LESSONS</th>
                                    <th>ASSIGNMENT</th>
                                    <th>ORDER</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($modules as $module)
                                    
                                    <tr>
                                        <td>{{ $module->id }}</td>
                                        <td>{{ $module->title }}</td>
                                        
                                        <td class="text-nowrap">{{ $module->level?->name }}</td>
                                        <td class="text-nowrap">{{ $module->awardingBody?->name }}</td>

                                        <td>
                                            @foreach ($module->courses as $course)
                                                <span class="badge bg-primary text-primary-fg fw-normal mb-1">{{ $course->title }} ({{ $course->level->name }})</span><br>
                                            @endforeach
                                        </td>

                                        <td>
                                            @foreach ($module->lessons as $lesson)
                                                <span class="badge bg-primary text-primary-fg fw-normal mb-1">{{ $lesson->title }}</span><br>
                                            @endforeach
                                        </td>

                                        <td class="text-nowrap">
                                            <div class="mb-1">
                                                @if($module->assignment_file)
                                                    <a href="{{ asset($module->assignment_file) }}" target="_blank" style="text-decoration:none"><i class="fa-regular fa-eye"></i> Assignment </a>
                                                @endif
                                            </div>
                                            <div>
                                                @if($module->sample_assignment_file)
                                                    <a href="{{ asset($module->sample_assignment_file) }}" target="_blank" style="text-decoration:none"><i class="fa-regular fa-eye"></i> Sample Assignment</a>
                                                @endif
                                            </div>
                                        </td>

                                        <td>{{ $module->order }}</td>

                                        <td>
                                            @if($module->status == 1)
                                                <span class="badge bg-green text-green-fg fw-normal">Active</span>
                                                
                                            @elseif($module->status == 0)
                                                <span class="badge bg-red text-red-fg fw-normal">Inactive</span>
                                            @else
                                                <span class="badge bg-yellow text-yellow-fg fw-normal">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if(auth()->user()?->canResource('admin_modules','edit'))
                                                <a href="{{ route('admin.module.edit', $module->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                    <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                                </a>
                                            @endif
                                            @if(auth()->user()?->canResource('admin_modules','delete'))
                                                <a href="{{ route('admin.module.destroy', $module->id) }}" class="text-red delete-item text-decoration-none">
                                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                                </a>
                                            @endif
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
                        {{ $modules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
