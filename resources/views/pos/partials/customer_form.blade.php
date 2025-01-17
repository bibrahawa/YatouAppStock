<!-- Modal for new customer -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="customerModal">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content rounded-lg border-0">
  
		<!-- Modal Header -->
		<div class="modal-header" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 10px 20px;">
		  <h5 class="modal-title font-weight-bold" id="exampleModalLabel" style="font-size: 1.2rem;">{{ trans('core.add_new_customer') }}</h5>
		  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
  
		<!-- Modal Body -->
		<div class="modal-body py-4 px-5">
  
		  <form>
			{!! csrf_field() !!}
  
			<!-- First and Last Name -->
			<div class="row mb-4">
			  <div class="col-md-6">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.first_name') }}
				  <span class="text-danger">*</span>
				</label>
				<input type="text" v-model="addCustomer.first_name" class="form-control form-control-lg shadow-sm" placeholder="John" required>
			  </div>
  
			  <div class="col-md-6">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.last_name') }}
				  <span class="text-danger">*</span>
				</label>
				<input type="text" v-model="addCustomer.last_name" class="form-control form-control-lg shadow-sm" placeholder="Doe" required>
			  </div>
			</div>
  
			<!-- Email and Phone -->
			<div class="row mb-4">
			  <div class="col-md-6">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.email') }}
				</label>
				<input type="email" v-model="addCustomer.email" class="form-control form-control-lg shadow-sm" placeholder="youremail@example.com">
			  </div>
  
			  <div class="col-md-6">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.phone') }}
				  <span class="text-danger">*</span>
				</label>
				<input type="tel" v-model="addCustomer.phone" class="form-control form-control-lg shadow-sm" placeholder="+1 (555) 555-5555" required>
			  </div>
			</div>
  
			<!-- Address -->
			<div class="row mb-4">
			  <div class="col-md-12">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.address') }}
				  <span class="text-danger">*</span>
				</label>
				<textarea v-model="addCustomer.address" class="form-control form-control-lg shadow-sm" rows="3" placeholder="123 Main St, City, Country" required></textarea>
			  </div>
			</div>
  
			<!-- Company Name -->
			<div class="row mb-4">
			  <div class="col-md-12">
				<label class="control-label font-weight-bold text-dark">
				  {{ trans('core.company_name') }}
				</label>
				<input type="text" v-model="addCustomer.company_name" class="form-control form-control-lg shadow-sm" placeholder="Company XYZ">
			  </div>
			</div>
  
		  </form>
		</div>
  
		<!-- Modal Footer -->
		<div class="modal-footer justify-content-between" style="border-top: 1px solid #e1e1e1;">
		  <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" ref="customerModalClose">
			<i class="fas fa-times-circle"></i> Close
		  </button>
		  <button @click.prevent="postNewCustomer" class="btn btn-primary btn-lg">
			<i class="fas fa-save"></i> Save changes
		  </button>
		</div>
  
	  </div>
	</div>
  </div>
  <!-- End Modal -->
  