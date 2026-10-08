
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ $permissions_form_route }}" method="POST" id="permissions_form">
    @csrf
    @if(isset($selectedDepartmentId))
        <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
    @elseif(isset($selectedTeamId))
        <input type="hidden" name="team_id" value="{{ $selectedTeamId }}">
    @elseif(isset($selectedRoleId))
        <input type="hidden" name="role_id" value="{{ $selectedRoleId }}">
    @endif
    
    <div class="table-responsive border rounded">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 35%;">Module</th>
                    <th style="width: 65%;">Permissions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedPermissions as $module => $subModules)
                    <tr class="table-active">
                        <td colspan="2">
                            <div class="form-check d-flex align-items-center">
                                @if(isset($targetEntity))
                                    <input class="form-check-input module-checkbox me-2" type="checkbox" data-module="{{ $module }}" id="mod_{{ $module }}">
                                @endif
                                <label class="form-check-label fw-bold mb-0" for="mod_{{ $module }}">
                                    {{ strtoupper(str_replace('_', ' ', $module)) }}
                                </label>
                            </div>
                        </td>
                    </tr>
                    
                    @foreach($subModules as $subModule => $perms)
                        <tr data-module-parent="{{ $module }}">
                            <td class="ps-4 align-middle">
                                <div class="form-check d-flex align-items-center">
                                    @if(isset($targetEntity))
                                        <input class="form-check-input submodule-checkbox me-2" type="checkbox" 
                                                data-module="{{ $module }}" 
                                                data-submodule="{{ $subModule }}" 
                                                id="sub_{{ $module }}_{{ $subModule }}">
                                    @endif
                                    <label class="form-check-label fw-bold" for="sub_{{ $module }}_{{ $subModule }}">
                                        @if($subModule !== 'General')
                                            {{ ucfirst(str_replace('_', ' ', $subModule)) }}
                                        @else
                                            <span class="text-muted">General Actions</span>
                                        @endif
                                    </label>
                                </div>
                            </td>
                            <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($perms as $perm)
                                            <div class="form-check me-2">
                                                @if(isset($targetEntity))
                                                    <input class="form-check-input permission-checkbox module-{{ $module }} submodule-{{ $module }}-{{ $subModule }} permission-item-checkbox" 
                                                            type="checkbox" 
                                                            name="permissions[]" 
                                                            value="{{ $perm['name'] }}" 
                                                            id="perm_{{ $perm['id'] }}"
                                                            data-module="{{ $module }}"
                                                            data-submodule="{{ $subModule }}"
                                                            {{ $perm['checked'] ? 'checked' : '' }}>
                                                @else
                                                    <i class="ti ti-circle-check text-muted" style="opacity: 0.5;"></i>
                                                @endif
                                                <label class="form-check-label {{ !isset($targetEntity) ? 'badge bg-light text-dark border' : '' }}" for="perm_{{ $perm['id'] }}">
                                                {{ ucfirst($perm['action']) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div id="save_btn_container" class="mt-4 text-end" style="{{ isset($targetEntity) ? '' : 'display:none;' }}">
        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
            <i class="ti ti-device-floppy me-2"></i>Save Permissions
        </button>
    </div>
    @if(!isset($targetEntity))
        <div class="alert alert-info d-inline-block mb-0 mt-3">
            <i class="ti ti-info-circle me-1"></i> Select a Department or Role above to manage permissions
        </div>
    @endif
</form>
