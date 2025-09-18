@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MANAGE PERMISSIONS ({{ $user->name }})</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.user.permission.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @php
                                $actions   = ['view','create','edit','delete'];             
                            @endphp
                            
                            @foreach ($roles as $role)
                                <div class="card shadow mb-4" data-scope="role-{{ $role->id }}">
                                    <div class="card-header py-3 d-flex align-items-center" data-scope="role-{{ $role->id }}">
                                        <h3 class="m-0 font-weight-bold text-primary">{{ ucwords($role->name) }} Role</h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="fs-5">Permission Name</th>
                                                    <th class="fs-5">Display Name</th>
                                                    
                                                    @foreach ($actions as $a)
                                                        @php $mid = "master-{$role->id}-{$a}"; @endphp
                                                        <th class="text-center py-3 fs-5">
                                                            <div class="d-flex flex-column align-items-center gap-1">
                                                            <label for="{{ $mid }}" class="mb-1">{{ $a }}</label>
                                                            <input
                                                                id="{{ $mid }}"
                                                                type="checkbox"
                                                                class="js-col-master"
                                                                data-scope="role-{{ $role->id }}"
                                                                data-action="{{ $a }}"
                                                            >
                                                            </div>
                                                        </th>
                                                    @endforeach
                                                    <!-- OPTIONAL: row master column -->
                                                    <th class="text-center py-3 fs-5">
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <label class="mb-1">All</label>
                                                            <input type="checkbox" class="js-all-master" data-scope="role-{{ $role->id }}">
                                                        </div> 
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($role->permissions->sortBy('display_name') as $permission)
                                                @php $pvt = optional($userPerms->get($permission->id))->pivot; @endphp
                                                <tr>
                                                    <td class="text-nowrap">{{ $permission->name }}</td>
                                                    <td class="text-nowrap">{{ $permission->display_name }}</td>

                                                    @foreach ($actions as $a)
                                                    @php $field = "permission[{$permission->id}][$a]"; @endphp
                                                    <td class="text-center">
                                                        <input type="hidden" name="{{ $field }}" value="0">
                                                        <input type="checkbox"
                                                            name="{{ $field }}"
                                                            value="1"
                                                            class="js-permission"
                                                            data-action="{{ $a }}"
                                                            data-permission="{{ $permission->id }}"
                                                            {{ ($pvt?->{"can_$a"} ?? false) ? 'checked' : '' }}>
                                                    </td>
                                                    @endforeach

                                                    <!-- OPTIONAL: row master -->
                                                    <td class="text-center">
                                                    <input type="checkbox"
                                                            class="js-row-master"
                                                            data-scope="role-{{ $role->id }}"
                                                            data-permission="{{ $permission->id }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                            <div class="d-flex justify-content-end mt-3">
                                <div class="add_course_basic_info_input">
                                    <div class="add_course_basic_info_input d-flex gap-2">
                                        <button type="submit" name="action" value="save_exit" class="btn btn-primary mt_20">Update</button>
                                        <button type="submit" name="action" value="save_stay" class="btn btn-secondary mt_20">Update & Stay</button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        // Column master => toggle all checkboxes for that action within the role scope
        document.querySelectorAll('.js-col-master').forEach(function (master) {
            master.addEventListener('change', function () {
            const scope  = master.dataset.scope;
            const action = master.dataset.action;
            document.querySelectorAll(`[data-scope="${scope}"] .js-permission[data-action="${action}"]`)
                .forEach(cb => cb.checked = master.checked);
            refreshMasters(scope);
            });
        });

        // Row master => toggle all actions for that permission id in this scope
        document.querySelectorAll('.js-row-master').forEach(function (master) {
            master.addEventListener('change', function () {
            const scope = master.dataset.scope;
            const permission  = master.dataset.permission;
            document.querySelectorAll(`[data-scope="${scope}"] .js-permission[data-permission="${permission}"]`)
                .forEach(cb => cb.checked = master.checked);
            refreshMasters(scope);
            });
        });

        // NEW: single "All" checkbox in header => toggle every checkbox within the role scope
        document.querySelectorAll('.js-all-master').forEach(function (master) {
            master.addEventListener('change', function () {
            const scope = master.dataset.scope;
            document.querySelectorAll(`[data-scope="${scope}"] .js-permission`)
                .forEach(cb => cb.checked = master.checked);
            refreshMasters(scope);
            });
        });

        // Section buttons
        document.querySelectorAll('.js-check-all, .js-uncheck-all').forEach(function (btn) {
            btn.addEventListener('click', function () {
            const scope = btn.dataset.scope;
            const boxes = document.querySelectorAll(`[data-scope="${scope}"] .js-permission`);
            if (btn.classList.contains('js-check-all'))  boxes.forEach(cb => cb.checked = true);
            if (btn.classList.contains('js-uncheck-all'))boxes.forEach(cb => cb.checked = false);
            refreshMasters(scope);
            });
        });

        // Keep master checkboxes in sync when user clicks individual boxes
        document.querySelectorAll('.js-permission').forEach(function (cb) {
            cb.addEventListener('change', function () {
            // find role scope from closest card
            const card  = cb.closest('[data-scope]');
            if (!card) return;
            const scope = card.getAttribute('data-scope');
            refreshMasters(scope);
            });
        });

        // initial state
        document.querySelectorAll('[data-scope]').forEach(sec => refreshMasters(sec.getAttribute('data-scope')));

        function refreshMasters(scope) {
            // Update column masters (checked/indeterminate)
            document.querySelectorAll(`[data-scope="${scope}"] .js-col-master`).forEach(function (m) {
            const action = m.dataset.action;
            const boxes  = Array.from(document.querySelectorAll(
                `[data-scope="${scope}"] .js-permission[data-action="${action}"]`
            ));
            const on  = boxes.filter(cb => cb.checked).length;
            m.indeterminate = on > 0 && on < boxes.length;
            m.checked = on > 0 && on === boxes.length;
            });

            // Update row masters
            document.querySelectorAll(`[data-scope="${scope}"] .js-row-master`).forEach(function (m) {
            const permission  = m.dataset.permission;
            const boxes = Array.from(document.querySelectorAll(
                `[data-scope="${scope}"] .js-permission[data-permission="${permission}"]`
            ));
            const on  = boxes.filter(cb => cb.checked).length;
            m.indeterminate = on > 0 && on < boxes.length;
            m.checked = on > 0 && on === boxes.length;
            });

            // NEW: Update the single "All" header checkbox
            const allMaster = document.querySelector(`[data-scope="${scope}"] .js-all-master`);
            if (allMaster) {
            const allBoxes = Array.from(document.querySelectorAll(
                `[data-scope="${scope}"] .js-permission`
            ));
            const on = allBoxes.filter(cb => cb.checked).length;
            allMaster.indeterminate = on > 0 && on < allBoxes.length;
            allMaster.checked = on > 0 && on === allBoxes.length;
            }
        }
        });
    </script>

@endpush