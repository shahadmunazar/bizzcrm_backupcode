<div class="tab-pane" id="bottom-justified-tab2">
							<div class="card">

							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<h4 class="page-title">Pending Letter</h4>
									</div>

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search...">
											</div>
										</div>
								</div>
								<!-- /Filter -->

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Employee Name</th>
												<th>Letter Type</th>
												<th>Application Date</th>
												<th>Requested By</th>
												<th class="text-end">Action</th>
										</tr>
										</thead>
										<tbody>
										@foreach($pendingLetters as $letter)
										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>{{ $letter->user->name ?? '---' }}</td>
											<td>{{ $letter->letter_type }}</td>
											<td>{{ \Carbon\Carbon::parse($letter->applied_date)->format('d M Y') }}</td>
											<td>{{ $letter->requested_by }}</td>
											<td>
												<div class="hstack gap-2 fs-15 text-end">
													<a class="btn btn-icon btn-sm btn-light ajax-delete" href="{{ route('employee.letter.delete', ['box_slug' => request()->route('box_slug'), 'id' => $letter->id]) }}" data-confirm="Are you sure?"><i class="ti ti-trash text-danger"></i></a>
												</div>
											</td>
										</tr>
										@endforeach
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
								<!-- /Campaign List -->

							</div>
						</div>
						</div>