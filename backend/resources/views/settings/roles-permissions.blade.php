@extends('layouts.main')
@section('content')
@section('title','User Roles')
	<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Roles</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="roles-permissions.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12 col-lg-12">
                                <div class="row">
                                    <!-- Departments Section -->
                                    <div class="col-md-12 mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0">Departments for {{ $box->name }}</h5>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_department">
                                                    <i class="ti ti-plus me-1"></i>Add Department
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                                <th class="text-end">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($departments as $dept)
                                                                <tr>
                                                                    <td>{{ $dept->name }}</td>
                                                                    <td>
                                                                        <span class="badge {{ $dept->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                                            {{ $dept->status == 1 ? 'Active' : 'Inactive' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <div class="dropdown dropdown-action">
                                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-edit me-2"></i>Edit</a>
                                                                                <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Roles Section -->
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0">System Roles & Default Access</h5>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_role">
                                                    <i class="ti ti-square-rounded-plus me-1"></i>Add Role
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Role</th>
                                                                <th>Permissions Count</th>
                                                                <th class="text-end">Operations</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($roles as $role)
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar avatar-sm bg-primary-light me-2">
                                                                                <i class="ti ti-shield text-primary"></i>
                                                                            </div>
                                                                            <div>
                                                                                <h6 class="mb-0">{{ ucfirst($role->name) }}</h6>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-info-light text-info">
                                                                            {{ $role->permissions->count() }} Actions
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <div class="d-flex justify-content-end gap-2">
                                                                            <a href="{{ box_route('manage_users.permissions', ['role_id' => $role->id]) }}" 
                                                                               class="btn btn-outline-primary btn-sm">
                                                                                <i class="ti ti-lock-access me-1"></i>Manage Access
                                                                            </a>
                                                                            <div class="dropdown dropdown-action">
                                                                                <a href="#" class="btn btn-link dropdown-toggle p-0" data-bs-toggle="dropdown">
                                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                                </a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <a class="dropdown-item" href="#"><i class="far fa-edit me-2"></i>Edit Title</a>
                                                                                    <a class="dropdown-item" href="#"><i class="far fa-trash-alt me-2"></i>Remove</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
		<!-- /Page Wrapper -->

		<!-- Add Role -->
		<div class="modal fade" id="add_role" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Role</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="roles-permissions.html">
						<div class="modal-body">
							<div class="mb-0">
								<label class="col-form-label">Role Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center justify-content-end m-0">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Create</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Role -->

		<!-- Edit Role -->
		<div class="modal fade" id="edit_role" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Role</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="roles-permissions.html">
						<div class="modal-body">
							<div class="mb-0">
								<label class="col-form-label">Role Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="Admin">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center justify-content-end m-0">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Save Changes</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Edit Role -->
		<!-- Add Department Modal -->
		<div class="modal fade" id="add_department" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Create New Department</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="#">
						<div class="modal-body">
							<div class="mb-3">
								<label class="col-form-label">Department Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" placeholder="e.g. Sales, Marketing, IT">
							</div>
                            <div class="mb-0">
                                <label class="col-form-label">Default Permission Set</label>
                                <select class="form-select">
                                    <option value="">-- Select Default Template --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }} Template</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Users in this department will get these permissions by default.</small>
                            </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Create Department</button>
						</div>
					</form>
				</div>
			</div>
		</div>
        @endsection
