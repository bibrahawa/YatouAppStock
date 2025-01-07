@extends('layouts.pos')
@section('title')
    @parent
@stop
@section('main-content')
    <div class="panel panel-default" id="app" style="padding: 20px; background-color: #f8f9fa;">
        <div class="panel-body">
            <div class="row">
                <form method="post">
                    <div class="col-md-4" style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; background-color: #ffffff;">
                        <h4 class="text-center">Sales</h4>
                        <div class="row pad5A">
                            <div class="col-md-10 pad5A">
                                <select class="form-control" v-model="customer" data-live-search="true">
                                    <option v-for="customerData in customers" :value="customerData.id">
                                        @{{customerData.first_name + ' ' + customerData.last_name}}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 pad5A">
                                <a class="btn btn-primary btn-block" data-toggle="modal" data-target="#customerModal">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" v-model="barcode" @keyup.prevent="getProductByBarcode" placeholder="Scan your barcode" />
                            </div>
                        </div>
                        <div style="min-height: 380px; overflow-y: scroll; border: 1px solid #ccc; border-radius: 8px; padding: 10px; margin-top: 10px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="{{settings('theme')}} pos-table-header">
                                        <th width="30%" class="text-center">{{trans('core.product')}}</th>
                                        <th width="10%" class="text-center">{{trans('core.quantity')}}</th>
                                        <th width="25%" class="text-center">{{trans('core.unit_price')}}</th>
                                        <th width="25%" class="text-center">{{trans('core.sub_total')}}</th>
                                        <th width="10%" class="text-center">
                                            <i class="fa fa-trash"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in selectedProducts" :key="product.uuid">
                                        <td class="text-center">
                                            @{{ product.name }}
                                        </td>
                                        <td class="text-center">
                                            <input type="text" v-model='product.sell_quantity' class="form-control text-center" onkeypress='return event.charCode <= 57 && event.charCode != 32' @keyup.prevent="addQuantity(product)">
                                        </td>
                                        <td class="text-center">
                                            @{{ product.mrp }}
                                        </td>
                                        <td class="text-center">
                                            @{{ parseFloat(product.mrp * product.sell_quantity).toFixed(2)}}
                                        </td>
                                        <td @click.prevent="removeFromSelected(product)" class="text-center" style="cursor: pointer;">
                                            <i class="fa fa-times text-danger"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--POS Table footer-->
                        <div class="col-md-12 {{settings('theme')}}" style="margin-bottom: 10px;">
                            <div class="row pos-footer">
                                <div class="col-md-12 padLpadR0">
                                    <table class="pos-table">
                                        <tr>
                                            <td width="25%" height="25px">
                                                {{trans('core.total_item')}}:
                                            </td>
                                            <td width="25%" height="25px" align="right">
                                                <strong>@{{totalQuantity}}</strong>
                                            </td>
                                            <td width="25%" height="25px" align="right">
                                                {{trans('core.total')}}:
                                            </td>
                                            <td width="25%" height="25px" align="right">
                                                <strong>@{{subTotal}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row pos-footer">
                                <div class="col-md-12 padLpadR0">
                                    <table class="pos-table">
                                        <tr>
                                            <td width="30%" height="25px">
                                                {{trans('core.discount')}}:
                                            </td>
                                            <td width="20%" height="25px">
                                                <input type="text" v-model='discount' class="form-control" placeholder="Discount" />
                                            </td>
                                            <td width="25%" height="25px" align="right">
                                                {{trans('core.amount')}}:
                                            </td>
                                            <td width="25%" height="25px" align="right">
                                                <strong>@{{discountAmount}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row pos-footer">
                                <div class="col-md-12 padLpadR0">
                                    <table class="pos-table">
                                        <tr>
                                            <td width="75%" height="25px" align="right">
                                                {{trans('core.vat')}}:
                                            </td>
                                            <td width="50%" height="25px" align="right">
                                                <strong>@{{invoiceTax}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row pos-total">
                                <div class="col-md-12 padLpadR0">
                                    <table class="pos-table">
                                        <tr>
                                            <td width="50%" height="30px">
                                                {{trans('core.total_payable')}}:
                                            </td>
                                            <td width="50%" height="30px" align="right">
                                                <strong id="total_payable">@{{netTotal}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 pad10A">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-danger" style="border-radius: 5px;">
                                            {{trans('core.cancel')}}
                                        </button>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#paymentModal" style="border-radius: 5px;">
                                            {{trans('core.payment')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-md-8">
                    <div class="panel panel-default" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                        <div class="panel-body">
                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                <div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 10px;">
                                    <input type="text" class="form-control" style="border: 1px solid #3a3a3a; color: #010101;" placeholder="Search" v-model="search" @keyup.prevent="getProductBySearch" />
                                </div>
                            </div>
                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                <div class="col-md-12 pos-cat-div">
                                    <div class="regular slider" style="width: 100%">
                                        <a data-toggle="tab" href="#all" class="pos-single-cat {{settings('theme')}} active" @click="loadProducts('all')">
                                            Frequent
                                        </a>
                                        @foreach($categories as $category)
                                            <a data-toggle="tab" href="#tab{{$category->id}}" class="pos-single-cat {{settings('theme')}}" @click="loadProducts({{ $category->id }})">
                                                {{$category->category_name}}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!--Show the products-->
                            <div style="min-height: 535px;" v-if="loading">
                                <center>
                                    <div id="loader">
                                        <div class="a"></div>
                                        <div class="b"></div>
                                        <div class="c"></div>
                                        <div class="d"></div>
                                    </div>
                                </center>
                            </div>
                            <div v-else class="" style="min-height: 535px;">
                                <div role="allTab" class="tab-pane active" id="all">
                                    <div class="col-md-12">
                                        <div class="col-md-4 pos-product-col" v-for="product in products" :key="product.id" @click.prevent="addToSelected(product)" style="min-height: 200px; background-color: #FFF; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; padding: 10px;">
                                            <center>
                                                <img v-if="product.image" :src="'/uploads/products/' + product.image" class="img-responsive img-rounded" :alt="product.name" style="max-height: 100px;">
                                                <img v-else src="{{asset('uploads/products/8NKeIGlWVSCE.png')}}" :alt="product.name" class="img-responsive img-rounded" style="max-height: 100px;">
                                                <p style="min-height: 60px; font-weight: bold;">@{{product.name}}</p>
                                                <small v-if="product.quantity > 0" class="text-success">
                                                    <b>In Stock: @{{product.quantity}}</b>
                                                </small>
                                                <small v-else class="text-danger">Out Of Stock</small>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                @foreach($categories as $category)
                                    <div role="tabpanel" class="tab-pane" id="tab{{$category->id}}">
                                        <div class="col-md-12">
                                            <div class="col-md-4 pos-product-col" v-for="product in products" :key="product.id" @click.prevent="addToSelected(product)" style="min-height: 200px; background-color: #FFF; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; padding: 10px;">
                                                <center>
                                                    <img v-if="product.image" :src="'/uploads/products/' + product.image" :alt="product.name" class="img-responsive img-rounded" style="max-height: 100px;">
                                                    <img v-else src="{{asset('uploads/products/8NKeIGlWVSCE.png')}}" :alt="product.name" class="img-responsive img-rounded" style="max-height: 100px;">
                                                    <p>@{{product.name}}</p>
                                                    <small v-if="product.quantity >= 0" class="text-success">
                                                        Stock: @{{product.quantity}}
                                                    </small>
                                                    <small v-else class="text-danger">Out Of Stock</small>
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Create customer modal body-->
        @include('pos.partials.customer_form')
        <!--Ends-->
        <!--Payment Modal Starts-->
        @include('pos.partials.pos-payment')
        <!--Payment Modal Ends-->
    </div>
@stop
@section('js')
    @parent
    <script src="/assets/js-core/lodash.js"></script>
    <script src="/assets/js-core/vue.js"></script>
    <script src="/assets/js-core/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        var app = new Vue({
            el: '#app',
            data: {
                customers: [],
                customer: '1',
                addCustomer: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    address: '',
                    company_name: '',
                    client_type: 'retailer'
                },
                products: [],
                selectedProducts: {},
                barcode: '',
                discount: 0,
                enableInvoiceTax: {{ settings('invoice_tax') ?: 0 }},
                invoice_tax_rate: {{ settings('invoice_tax_rate') ?: 0 }},
                invoice_tax_type: {{ settings('invoice_tax_type') ?: 2 }},
                paid: 0,
                paying_method: 'cash',
                reference_no: '',
                search: '',
                loading: false,
            },
            computed: {
                totalQuantity: function () {
                    return _.reduce(this.selectedProducts, function(result, product) {
                        return result + parseInt(product.sell_quantity)
                    }, 0)
                },
                subTotal: function () {
                    subtotal = _.reduce(this.selectedProducts, function(result, product) {
                        return result + parseFloat(product.mrp) * parseFloat(product.sell_quantity)
                    }, 0)
                    return subtotal.toFixed(2)
                },
                discountAmount: function () {
                    var discountAmount = this.discount
                    var isPercentage = (this.discount.toString().indexOf('%') !== -1) ? true : false
                    if(isPercentage) {
                        var amount = discountAmount.replace("%", "");
                        discountAmount = this.subTotal * (1 * amount / 100)
                    }
                    return discountAmount
                },
                invoiceTax: function () {
                    var invoice_tax_amount = 0
                    if(this.enableInvoiceTax == 1){
                        if(this.invoice_tax_type == 1){
                            invoice_tax_amount = (this.invoice_tax_rate * (this.subTotal - this.discountAmount)) / 100
                        }else{
                            invoice_tax_amount = this.invoice_tax_rate
                        }
                    }
                    return invoice_tax_amount
                },
                netTotal: function () {
                    return parseFloat((this.subTotal + this.invoiceTax) - this.discountAmount).toFixed(2)
                },
            },
            methods:{
                addQuantity: function (product) {
                    var quantityToAdd = parseInt(product.sell_quantity)
                    this.addToSelected(product, quantityToAdd, true)
                },
                resetClient: function () {
                    this.addCustomer = {
                        first_name: '',
                        last_name: '',
                        email: '',
                        phone: '',
                        address: '',
                        company_name: '',
                        client_type: 'retailer'
                    }
                },
                postNewCustomer: function () {
                    var self = this
                    axios.post('/api/v1/customer/save', this.addCustomer)
                        .then(function (response) {
                            console.log(JSON.stringify(response))
                            self.loadClients()
                            self.resetClient()
                            $(self.$refs.customerModalClose).trigger('click')
                        })
                        .catch (function (response) {
                            console.log(JSON.stringify(response))
                        })
                },
                loadClients: function () {
                    var self = this
                    axios.get('/api/client').then(function (response) {
                        self.customers = response.data
                    }, function (response) {
                        console.log(response)
                    })
                },
                loadProducts: function (data) {
                    var self = this
                    self.loading = true;
                    self.products = []
                    var getUrl = (data === 'all') ?
                                '/api/v1/products' :
                                '/api/v1/category/' + data + '/products'
                    axios.get(getUrl)
                        .then(function (response) {
                            self.products = response.data.data
                            self.loading = false
                        })
                        .catch(function (response) {
                            console.log(JSON.stringify(response))
                        })
                },
                getProductBySearch: function () {
                    var self = this
                    self.loading = true;
                    self.products = []
                    var searchUrl = '/api/v1/product-by-search/' + self.search
                    axios.get(searchUrl)
                        .then(function (response) {
                            self.products = response.data.data
                            self.loading = false
                        })
                        .catch(function (response) {
                            console.log(JSON.stringify(response))
                        })
                },
                getProductByBarcode: _.debounce(function () {
                    var self = this
                    axios.get('/api/v1/product-by-barcode/' + self.barcode)
                        .then(function (response) {
                            if (response.data.found === true) {
                                self.addToSelected(response.data.product)
                                self.barcode = ''
                            }
                        })
                        .catch(function (response) {
                            console.log(JSON.stringify(response))
                        })
                }, 300),
                addToSelected: function (product, quantityToAdd = 1, fresh = false) {
                    var exists = this.selectedProducts[product.id]
                    if (fresh) {
                        product.sell_quantity = 0
                    }
                    if (exists !== undefined) {
                        product.sell_quantity = parseInt(this.selectedProducts[product.id].sell_quantity) + quantityToAdd
                        product.uuid = _.uniqueId('product_')
                        this.selectedProducts = _.omit(this.selectedProducts, product.id)
                        this.$set(this.selectedProducts, product.id, product)
                    } else {
                        product['sell_quantity'] = 1
                        product['uuid'] = _.uniqueId('product_')
                        this.$set(this.selectedProducts, product.id, product)
                    }
                },
                removeFromSelected: function (product) {
                    this.selectedProducts = _.omit(this.selectedProducts, product.id)
                },
                postSell: function () {
                    var self = this
                    if(self.totalQuantity <= 0){
                        swal("Sorry", "Please Select Product Before Payment ", "warning");
                        return false;
                    }
                    if(parseFloat(self.paid) < self.netTotal){
                        swal("Sorry", "Paid amount can't be less than Net Total " + self.netTotal, "warning");
                        return false;
                    }
                    axios.post('/admin/pos/sell/save', {customer: this.customer, sells: this.selectedProducts, paid: this.paid, method: this.paying_method, discount_amount: this.discountAmount, invoice_tax: this.invoiceTax, })
                        .then(function (response) {
                            swal('success', 'success', 'success')
                            var transactionId = response.data.id
                            window.location.href = 'pos/sell/invoice/' + transactionId;
                            console.log(JSON.stringify(response))
                        })
                        .catch (function (response) {
                            alert('error')
                            console.log(JSON.stringify(response))
                        })
                },
            },
            mounted: function () {
                this.loadClients()
                this.loadProducts('all')
            }
        });
    </script>
@stop
