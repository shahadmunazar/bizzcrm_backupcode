@extends('layouts.main')
@section('title', 'HR Documents')
@section('content')

<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">HR Toolkit</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="leads.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
											<i class="ti ti-refresh-dot"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search...">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upload_document_modal"><i class="ti ti-square-rounded-plus me-2"></i>Add Document</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
									    <h4 class="page-title">Employee Policies</h4>


									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<!-- You can add simple search/filter for documents here later if needed -->
									</div>
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									@if(session('success'))
										<div class="alert alert-success">{{ session('success') }}</div>
									@endif
									@if(session('error'))
										<div class="alert alert-danger">{{ session('error') }}</div>
									@endif
									<table class="table" id="hr_documents_table">
										<thead class="thead-light">
											<tr>
												<th>Sr. No.</th>
												<th>Document Name</th>
												<th>Document Category</th>
												<th>Uploaded By</th>
												<th class="text-start">Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach($documents as $key => $doc)
											<tr>
											    <td>{{ $key + 1 }}</td>
											    <td>{{ $doc->name }}</td>
											    <td>{{ $doc->category }}</td>
												<td>{{ $doc->uploaded_by ? \App\Models\User::find($doc->uploaded_by)->name : 'System' }}</td>
											    <td>
												<div class="hstack gap-2 fs-15">
													<a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-icon btn-sm btn-light" title="Download"><i class="ti ti-download text-blue"></i></a>
													<a href="{{ route('hr.employess.hr.documents.delete', ['box_slug' => request()->route('box_slug'), 'id' => $doc->id]) }}" class="btn btn-icon btn-sm btn-light" onclick="return confirm('Are you sure you want to delete this document?');" title="Delete"><i class="ti ti-trash text-danger"></i></a>
												</div>
												</td>
											</tr>
											@endforeach
											@if($documents->isEmpty())
											<tr>
												<td colspan="5" class="text-center">No documents found.</td>
											</tr>
											@endif
										</tbody>
									</table>
								</div>
								<div class="row align-items-center">
									<div class="col-md-6">
										<div class="datatable-length"></div>
									</div>
									<div class="col-md-6">
										<div class="datatable-paginate"></div>
									</div>
								</div>
								<!-- /Contact List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Upload Document Modal -->
		<div class="modal fade" id="upload_document_modal" tabindex="-1" aria-labelledby="upload_document_modal_label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form action="{{ route('hr.employess.hr.documents.store', ['box_slug' => request()->route('box_slug')]) }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="modal-header border-bottom">
							<h5 class="modal-title" id="upload_document_modal_label">Upload HR Document</h5>
							<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="modal" aria-label="Close">
								<i class="ti ti-x"></i>
							</button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label class="col-form-label">Document Name <span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Category</label>
								<input type="text" name="category" class="form-control" placeholder="e.g. Agreements, Policies, Forms">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Document File (PDF, DOC, IMG) <span class="text-danger">*</span></label>
								<input type="file" name="document_file" class="form-control" required>
							</div>
						</div>
						<div class="modal-footer border-top text-end">
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Upload Document</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Upload Document Modal -->


@endsection
