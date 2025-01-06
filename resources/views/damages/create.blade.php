@extends('app')

@section('title')
	Add Damage Items
@stop

@section('breadcrumb')
	Add Damage Items
@stop

@section('main-content')

<div class="panel-body">
	<h3 class="title-hero">Add Damage Items</h3>

	<form method="post" id="app">
		{{ csrf_field() }}
    	<div>
    		<table class="table table-bordered bg-sells">
    			<thead class="{{settings('theme')}}">
					<tr style="color: #FFF !important;">
						<td class="text-center font-white" style="width: 35%;">
							{{trans('core.product')}}
						</td>
						<td class="text-center font-white"  style="width: 30%;">
							{{trans('core.quantity')}}
						</td>
						<td class="text-center font-white" style="width: 30%;">
							Note
						</td>
						<td style="width: 5%;">&nbsp;</td>
					</tr>
				</thead>

				<tbody>
					<tr 
						is="sell"
						v-for="sell in sells" 
						:id="sell.id"
						:sell="sell"
						:enable_product_tax="{{ settings('product_tax') }}"
						:add="addInput"
						:remove="removeInput"
					></tr>
				</tbody>

				<tfoot>
					<!-- Date -->
					<tr>
						<td colspan="2" @if(!rtlLocale()) style="text-align: right; font-weight: bold;" @endif>
							{{trans('core.date')}} &nbsp;&nbsp;
						</td>
						<td colspan="2">
							<input type="text" ref="sellDate" class="datepicker form-control text-center">
						</td> 
					</tr>
					<!-- Ends -->

					<tr>
						<td colspan="6">
							<button type="submit" @click.prevent="postForm" :disabled="submitted" class="btn btn-success pull-right"> 
								<i class="fa fa-spinner fa-pulse fa-fw" v-if="submitted"></i> 
								{{trans('core.submit')}} 
							</button>
						</td>
					</tr>
				</tfoot>
    		</table>  	 
		</div>
	</form>
</div>


<template id="sell">
	<tr>
		<td>
			<select class="form-control selectPickerLive" @change="setPrice" v-model="sell.product_id" data-live-search="true">
				<option>{{trans('core.select_product')}}</option>
				@foreach($products as $product)
					<option 
						value="{{$product->id}}" 
						data-price="{{$product->mrp}}"
						data-cost_price="{{$product->cost_price}}"
						data-minprice="{{$product->minimum_retail_price}}"
						data-quantity="{{$product->quantity}}"
						data-taxrate="{{$product->tax ? $product->tax->rate : 0}}"
						data-taxtype="{{$product->tax ? $product->tax->type : null }}"
					>
						{{$product->name}} ({{$product->code}})
					</option>
				@endforeach
			</select>
		</td>

		
		<td>
			<input type="text" v-model="sell.quantity" class="form-control text-center">
		</td>


		<td>
			<input type="text" v-model="sell.note" class="form-control text-center">
		</td>

		<td>
			<button @click.prevent="remove(id)" class="btn btn-danger" v-if="id != 1">
				<i class="fa fa-times"></i>
			</button>
			<button @click.prevent="add()" class="btn btn-success" v-else >
				<i class="fa fa-plus"></i>
			</button>
		</td>
	</tr>
</template>
@stop

@section('js')
    @parent
	<script src="/assets/js-core/vue.min.js"></script>
    <script src="/assets/js-core/axios.min.js"></script>
    
    <script>
    	$(document).ready(function () {
    		$('.selectPickerLive').selectpicker();
    	})
	    
    	axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
		axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    	Vue.component('sell', {
    		template: '#sell',
    		props: ['id', 'sell', 'add', 'remove','enable_product_tax'],
    		data: function () {
	    		return {}
	    	},
	    	methods: {
	    		setPrice: function (event) {
	    			var selectedPrice = $('option:selected', event.target).data('price')
	    		}
	    	},
	    	mounted: function () {
	    		
	    	}
    	})

    	var app = new Vue({
		    el: '#app',
		    data: {
		    	sells: [
		    		{ 
		    			id: 1, 
		    			quantity: 1,
		    			product_id: 0,
		    			note: '',
		    		},
		    	],
		    	submitted: false,
		    },
		    computed: {
		    	
		    },
		    methods:{
		        addInput: function () {
		        	var newInputId = 1
		        	for (var i = 0; i < this.sells.length; i++) {
		        		newInputId = this.sells[i].id + 1
		        	}
		        	this.sells.push({ id: newInputId, note: '', quantity: 1, product_id: 0})
		        	this.$nextTick(function () {
		        		$('.selectPickerLive').selectpicker()
		        	})
		        },
		        removeInput: function (id) {
		        	console.log('PASSED ID', id)
		           var index = this.sells.findIndex(function (sell) {
		           		return sell.id === id
		           })
		           console.log('INDEX', index)
		           this.sells.splice(index, 1)
		        },
		        postForm: function () {
		        	this.submitted = true

		        	var self = this
					axios.post('/admin/damaged-products/add', 
									{ 
										sells: this.sells,
										date: this.$refs.sellDate.value 
									}
								)
					  .then(function (response) {
					    console.log(JSON.stringify(response.data));
					    window.location.href = '{{route("damage.index")}}';
					  })
					  .catch(function (error) {
					  	self.submitted = false
					  	console.log(JSON.stringify(error))
					    swal('Something went wrong..', error.response.data.message, 'error')
					  });
		        },
		        bootExternalLibraries: function () {
		        	this.$nextTick(function () {
						$('.datepicker').datetimepicker({
					          format : 'YYYY-M-D H:mm:ss'
					      })
		        	})
		        }
		    },
		    created: function () {
		    	this.bootExternalLibraries()
		    }
		});
    </script>
@stop