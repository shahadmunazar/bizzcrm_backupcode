<div class="tab-pane" id="bottom-justified-tab7">
	<div class="card">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
				<h4 class="page-title">Letter Types</h4>
				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_letter_type_modal">Add New Letter Type</button>
			</div>

			<div class="table-responsive custom-table">
				<table class="table">
					<thead class="thead-light">
					<tr>
						<th>Type Name</th>
						<th>Status</th>
						<th class="text-end">Action</th>
					</tr>
					</thead>
					<tbody id="letter-types-tbody">
					<!-- Data will be loaded via jQuery AJAX on page load -->
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Generic Edit Modal for Letter Types -->
	<div class="modal custom-modal fade" id="edit_letter_type_modal" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Letter Type</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x"></i>
					</button>
				</div>
				<div class="modal-body">
					<!-- Action URL will be populated via JS -->
					<form action="" method="POST" class="ajax-form" id="edit_letter_type_form">
						@csrf
						<div class="mb-3">
							<label class="col-form-label">Type Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="name" id="edit_letter_type_name" value="" required>
						</div>
						<div class="mb-3">
							<label class="col-form-label">Status <span class="text-danger">*</span></label>
							<select class="form-control select" name="status" id="edit_letter_type_status" required>
								<option value="Active">Active</option>
								<option value="Inactive">Inactive</option>
							</select>
						</div>
						<div class="modal-btn text-end">
							<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-danger">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>