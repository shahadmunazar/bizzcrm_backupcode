<div class="tab-pane" id="bottom-justified-tab6">
							<div class="card">

							<div class="card-body">


								<div class="col-md-12">
								    <form action="{{ route('letter.head.store', request()->route('box_slug')) }}" method="POST" enctype="multipart/form-data" class="ajax-form">
										@csrf
										<div class="mb-3">
												<label class="col-form-label">Letter Head Image <span class="text-danger">*</span></label>
												<div class="drag-attach">
													<input type="file" name="file" required>
													<div class="img-upload">
														<i class="ti ti-file-broken"></i>Select Letter Head
													</div>
												</div>
											</div>
										<div class="mb-3">
											<label class="col-form-label">Status <span class="text-danger">*</span></label>
											<select class="form-control select" name="status" required>
												<option value="Active">Active</option>
												<option value="Inactive">Inactive</option>
											</select>
										</div>
										<div class="text-start mb-4">
											<button type="submit" class="btn btn-danger">Upload Letter Head</button>
										</div>
									</form>
									
									<h5>Current Letter Heads</h5>
									<div class="row mt-3" id="letter-heads-container">
										<!-- Data will be loaded via jQuery AJAX on page load -->
									</div>
								</div>


							</div>
						</div>
						</div>