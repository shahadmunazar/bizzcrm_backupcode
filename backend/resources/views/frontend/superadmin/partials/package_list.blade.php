<div class="table-responsive custom-table">
    <table class="table" id="packages-table">
        <thead class="thead-light">
            <tr>
                <th class="no-sort">
                    <label class="checkboxs">
                        <input type="checkbox" id="select-all"><span class="checkmarks"></span>
                    </label>
                </th>
                <th class="no-sort"></th>
                <th>Plan Name</th>
                <th>Category</th>
                <th>Payment Mode</th>
                <th>Plan Type</th>
                <th>Total Subscribers</th>
                <th>Price</th>
                <th>Razorpay Plan ID</th>
                <th>Created Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="package-table-body">
            <!-- Table rows will be dynamically populated via AJAX and jQuery -->
        </tbody>
    </table>
</div>

<!-- Single Edit Plan Offcanvas Template -->
<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_edit">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Plan</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="" method="POST" class="edit-plan-form" id="edit_plan_form">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_plan_id">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Plan Name<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name" id="edit_plan_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Plan Type<span class="text-danger"> *</span></label>
                                <select class="select form-control" name="type" id="edit_plan_type" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Plan Category<span class="text-danger"> *</span></label>
                                <select class="select form-control" name="plan_type" id="edit_plan_category" required>
                                    <option value="subscription">Subscription</option>
                                    <option value="topup">Topup</option>
                                    <option value="whatsapp">WhatsApp Plan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Payment Mode<span class="text-danger"> *</span></label>
                                <select class="select form-control" name="payment_mode" id="edit_plan_payment_mode" required>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Price<span class="text-danger"> *</span></label>
                                <input type="number" step="0.01" class="form-control" name="price" id="edit_plan_price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Currency<span class="text-danger"> *</span></label>
                                <select class="select form-control" name="currency" id="edit_plan_currency" required>
                                            <option value="AED">AED (د.إ) - United Arab Emirates dirham</option>
                                            <option value="AUD">AUD ($) - Australian dollar</option>
                                            <option value="BGN">BGN (лв) - Bulgarian lev</option>
                                            <option value="BRL">BRL (R$) - Brazilian real</option>
                                            <option value="CAD">CAD ($) - Canadian dollar</option>
                                            <option value="CHF">CHF (Fr.) - Swiss franc</option>
                                            <option value="CNY">CNY (¥) - Chinese yuan</option>
                                            <option value="EUR">EUR (€) - Euro</option>
                                            <option value="GBP">GBP (£) - British pound</option>
                                            <option value="HKD">HKD ($) - Hong Kong dollar</option>
                                            <option value="IDR">IDR (Rp) - Indonesian rupiah</option>
                                            <option value="ILS">ILS (₪) - Israeli new shekel</option>
                                            <option value="INR" selected>INR (₹) - Indian rupee</option>
                                            <option value="JPY">JPY (¥) - Japanese yen</option>
                                            <option value="KES">KES (Sh) - Kenyan shilling</option>
                                            <option value="KRW">KRW (₩) - South Korean won</option>
                                            <option value="MXN">MXN ($) - Mexican peso</option>
                                            <option value="MYR">MYR (RM) - Malaysian ringgit</option>
                                            <option value="NGN">NGN (₦) - Nigerian naira</option>
                                            <option value="NZD">NZD ($) - New Zealand dollar</option>
                                            <option value="PHP">PHP (₱) - Philippine peso</option>
                                            <option value="PLN">PLN (zł) - Polish złoty</option>
                                            <option value="RUB">RUB (₽) - Russian ruble</option>
                                            <option value="SAR">SAR (ر.س) - Saudi riyal</option>
                                            <option value="SGD">SGD ($) - Singapore dollar</option>
                                            <option value="THB">THB (฿) - Thai baht</option>
                                            <option value="TRY">TRY (₺) - Turkish lira</option>
                                            <option value="TWD">TWD (NT$) - New Taiwan dollar</option>
                                            <option value="UAH">UAH (₴) - Ukrainian hryvnia</option>
                                            <option value="UGX">UGX (Sh) - Ugandan shilling</option>
                                            <option value="USD">USD ($) - United States dollar</option>
                                            <option value="UYU">UYU ($) - Uruguayan peso</option>
                                            <option value="UZS">UZS (so'm) - Uzbekistani soʻm</option>
                                            <option value="VES">VES (Bs.S.) - Venezuelan bolívar soberano</option>
                                            <option value="VND">VND (₫) - Vietnamese đồng</option>
                                            <option value="ZAR">ZAR (R) - South African rand</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Trial Period (Days)</label>
                                <input type="number" class="form-control" name="trial_period_days" id="edit_plan_trial_period" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Grace Period (Days)</label>
                                <input type="number" class="form-control" name="grace_period_days" id="edit_plan_grace_period" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="form-label">Status<span class="text-danger"> *</span></label>
                                <select class="select form-control" name="status" id="edit_plan_status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                                <div class="col-md-12 edit-whatsapp-fields" style="display:none;">
                                    <h6 class="mb-3 border-bottom pb-2">WhatsApp Plan Details</h6>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Discount Badge</label>
                                        <input type="text" class="form-control" name="discount_badge" id="edit_plan_discount_badge" placeholder="e.g. Save 20%">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Savings Text</label>
                                        <input type="text" class="form-control" name="savings_text" id="edit_plan_savings_text" placeholder="e.g. You save ₹1500">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Original Price</label>
                                        <input type="number" step="0.01" class="form-control" name="original_price" id="edit_plan_original_price">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Billing Cycle</label>
                                        <input type="text" class="form-control" name="billing_cycle" id="edit_plan_billing_cycle" placeholder="e.g. /Month">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Marketing Msg Cost</label>
                                        <input type="text" class="form-control" name="marketing_msg_cost" id="edit_plan_marketing_msg_cost" placeholder="e.g. ₹0.72">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Utility Msg Cost</label>
                                        <input type="text" class="form-control" name="utility_msg_cost" id="edit_plan_utility_msg_cost" placeholder="e.g. ₹0.30">
                                    </div>
                                </div>
                                <div class="col-md-6 edit-whatsapp-fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Button Text</label>
                                        <input type="text" class="form-control" name="button_text" id="edit_plan_button_text" placeholder="e.g. Subscribe Now">
                                    </div>
                                </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Plan Features</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">Add</th>
                                                <th>Feature</th>
                                                <th>Value / Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="edit_plan_features_list">
                                            <!-- Populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3 ">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_plan_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Plan</button>
            </div>
        </form>
    </div>
</div>
