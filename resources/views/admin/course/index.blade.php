@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">COURSES</h3>
                    <div class="card-actions">
                        @if(auth()->user()?->canResource('admin_courses','create'))
                            <a href="{{ route('admin.course.create') }}" class="btn btn-default">
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
                                    <th>LOGO</th>
                                    <th>TITLE</th>
                                    <th>CATEGORY</th>
                                    <th>MODULES</th>
                                    <th>PRICE</th>
                                    <th>DISCOUNT</th>
                                    <th>STUDENT</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courses as $course)
                                    
                                    <tr>
                                        <td>{{ $course->id }}</td>

                                        <td style="width: 100px;"><img src="{{ asset($course->logo) }}" alt="img" class="img-fluid w-10"></td>

                                        <td>{{ $course->title }} ({{ $course->level->name }})<br><small>{{ $course->awardingBody->name }}</small></td>
                                        <td>{{ $course->category->name }}</td>
                                        <td>
                                            @foreach ($course->modules as $module)
                                                <span class="badge bg-primary text-primary-fg fw-normal mb-1">{{ $module->title }}</span>
                                                <span class="badge badge-light text-dark fw-normal mb-1">{{ $module->awardingBody?->name }}</span>
                                                <br>
                                            @endforeach
                                        </td>
                                        <td>{{ currency_format($course->price) }}</td>
                                        <td>{{ currency_format($course->discount) }}</td>
                                        <td>{{ $course->enrollments_count }}</td>

                                        <td>
                                            @if($course->status == 'draft')
                                                <span class="badge bg-yellow text-yellow-fg fw-normal">Draft</span>
                                            @elseif($course->status == 'active')
                                            <span class="badge bg-green text-green-fg fw-normal">Active</span>
                                            @elseif($course->status == 'inactive')
                                            <span class="badge bg-red text-red-fg fw-normal">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()?->canResource('admin_courses','edit'))
                                                <a href="{{ route('admin.course.edit', $course->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                    <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()?->canResource('admin_courses','delete'))
                                                <a href="{{ route('admin.course.destroy', $course->id) }}" class="text-red delete-item text-decoration-none">
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
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
