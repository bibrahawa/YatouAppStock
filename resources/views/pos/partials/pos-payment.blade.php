<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content" style="border-radius: 10px;">
		<div class="modal-header" style="background-color: #007bff; color: white; border-radius: 10px 10px 0 0;">
		  <h5 class="modal-title" id="paymentModalLabel">Finalize Sale</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <!-- Payment Form -->
		  <div class="payment-form">
			<div class="row">
			  <div class="col-md-6 mb-3">
				<label for="paid">Amount Paid</label>
				<input type="text" v-model="paid" id="paid" class="form-control" placeholder="Enter amount" style="border-radius: 8px;">
			  </div>
			  <div class="col-md-6 mb-3">
				<label for="paying_method">Paying By</label>
				<select id="paying_method" v-model="paying_method" class="form-control" style="border-radius: 8px;">
				  <option value="cash">Cash</option>
				  <option value="card">Card</option>
				  <option value="mobile_money">Mobile Money</option>
				</select>
			  </div>
			</div>
		  </div>
  
		  <div class="cart-summary" style="background-color: #f9f9f9; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
			<div class="row">
			  <div class="col-md-12">
				<h5 style="font-weight: bold; color: #333; text-align: center;">Transaction Summary</h5>
			  </div>
			</div>
			
			<!-- Total Items & Total Payable -->
			<div class="row" style="margin-bottom: 15px;">
			  <div class="col-md-6">
				<div style="font-size: 16px; font-weight: bold; color: #555;">Total Items: <span style="font-weight: normal; color: #777;">@{{ totalQuantity }}</span></div>
			  </div>
			  <div class="col-md-6">
				<div style="font-size: 16px; font-weight: bold; color: #555;">Total Payable: <span style="font-weight: normal; color: #777;">@{{ netTotal | currency }}</span></div>
			  </div>
			</div>
		  
			<hr>
		  
			<!-- Due and Change Amount -->
			<div class="row">
			  <div class="col-md-6">
				<div class="d-flex justify-content-between align-items-center" style="color: #E74C3C; font-weight: bold;">
				  <span>Amount Due:</span>
				  <span>@{{ (netTotal - paid) >= 0 ? (netTotal - paid | currency) : '0' }}</span>
				</div>
			  </div>
			  <div class="col-md-6">
				<div class="d-flex justify-content-between align-items-center" style="color: #27AE60; font-weight: bold;">
				  <span>Change:</span>
				  <span>@{{ (paid - netTotal) >= 0 ? (paid - netTotal | currency) : '0' }}</span>
				</div>
			  </div>
			</div>
		  
			<hr>
		  
			<!-- Paying Method -->
			<div class="row">
			  <div class="col-md-12">
				<label for="paying-method" style="font-weight: bold;">Paying Method:</label>
				<select id="paying-method" v-model="paying_method" class="form-control" style="border-radius: 5px;">
				  <option value="cash">{{ trans('core.cash') }}</option>
				  <option value="card">{{ trans('core.card') }}</option>
				  <option value="cash + card">{{ trans('core.cash-plus-card') }}</option>
				  <option value="mobile_money">{{ trans('core.mobile-money') }}</option>
				  <option value="others">{{ trans('core.others') }}</option>
				</select>
			  </div>
			</div>
		  
			<!-- Submit Button -->
			<div class="row" style="margin-top: 20px;">
			  <div class="col-md-12">
				<button class="btn btn-primary btn-block" @click.prevent="postSell" style="border-radius: 5px; font-size: 16px;">
				  Submit Payment
				</button>
			  </div>
			</div>
		  </div>
		  

		</div>
		<div class="modal-footer">
		  <button class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px;">Close</button>
		  <button class="btn btn-primary btn-block" @click.prevent="postSell" style="border-radius: 8px;">
			Submit Payment
		  </button>
		</div>
	  </div>
	</div>
  </div>
  