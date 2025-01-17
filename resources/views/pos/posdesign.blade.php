@extends('layouts.pos')

@section('title')
    @parent
@stop

@section('css')
<style>
    .loader_page {
        border: 8px solid #f3f3f3; /* Gris clair */
        border-top: 8px solid #3498db; /* Bleu */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

@stop

@section('main-content')

    <div class="panel panel-default" id="app" style="display: none;">
        <div class="panel-body">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Section des produits -->
                <div class="col-span-2">
                    <div class="panel panel-default" style="border: 1px solid #ddd;">
                        <div class="panel-body">
                            <div class="relative w-full">
                                <input 
                                    type="text" 
                                    class="form-control border border-gray-300 rounded-lg pl-4 pr-12 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="Search" 
                                    v-model="search" 
                                    @keyup.prevent="getProductBySearch"
                                />
                                <i 
                                    class="fa fa-refresh absolute top-1/2 right-4 transform -translate-y-1/2 text-gray-500 cursor-pointer" 
                                    @click="loadProducts('all')">
                                </i>
                            </div>
                            <div v-if="loading" class="flex items-center justify-center min-h-[535px] bg-gray-100">
                                <div id="loader" class="flex space-x-2">
                                    <div class="w-5 h-5 bg-blue-600 rounded-full animate-bounce"></div>
                                    <div class="w-5 h-5 bg-blue-400 rounded-full animate-bounce delay-200"></div>
                                    <div class="w-5 h-5 bg-blue-200 rounded-full animate-bounce delay-400"></div>
                                </div>
                            </div>
                            
                            <div v-else class="min-h-[535px] bg-gray-50 py-6">
                                <div role="allTab" class="tab-pane active" id="all">
                                    <div class="container mx-auto px-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                            <div 
                                                v-for="product in products" 
                                                :key="product.id" 
                                                class="group relative flex flex-col items-center p-3 bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow transform hover:scale-105"
                                                @click.prevent="addToSelected(product)"
                                            >
                                                <!-- Badge Stock -->
                                                <div 
                                                    class="absolute top-2 right-1 px-5 py-3 text-sm font-semibold text-green-800 bg-green-200 rounded-full">
                                                    @{{ product.quantity }}
                                                </div>
                                                
                                                <!-- Product Image -->
                                                <img 
                                                    v-if="product.image" 
                                                    :src="'/uploads/products/' + product.image" 
                                                    class="w-28 h-28 object-contain mb-4 transition-transform duration-200 group-hover:scale-110"
                                                    :alt="product.name"
                                                >
                                                <img 
                                                    v-else 
                                                    src="{{ asset('uploads/products/8NKeIGlWVSCE.png') }}" 
                                                    alt="Placeholder Image" 
                                                    class="w-28 h-28 object-contain mb-4 transition-transform duration-200 group-hover:scale-110"
                                                >
                                                
                                                <!-- Product Name -->
                                                <p class="text-center text-base font-semibold text-gray-700 group-hover:text-blue-600 min-h-[40px]">
                                                    @{{ product.name }}
                                                </p>
                                                
                                                <!-- Product Quantity -->
                                                <small class="mt-2 text-xl font-semibold text-gray-500">
                                                    Prix: @{{ product.mrp }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Section du panier -->
                <div class="col-span-1">
                    <form method="post" style="border: 1px solid #ddd;" class="bg-white p-6 shadow-lg rounded-lg mx-auto">
                        <!-- Sélection du client -->
                        <div class="flex items-center gap-4 mb-6">
                            <select 
                            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200" 
                            v-model="customer" 
                            aria-label="Sélectionnez un client"
                            data-live-search="true"
                            >
                            <option v-for="customerData in customers" :value="customerData.id">
                                @{{ customerData.first_name + ' ' + customerData.last_name }}
                            </option>
                            </select>
                            <button 
                            type="button" 
                            class="p-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200" 
                            data-toggle="modal" 
                            data-target="#customerModal" 
                            aria-label="Ajouter un client"
                            >
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        
                        <!-- Saisie du code-barres -->
                        <div class="mb-6">
                            <div class="relative">
                            <input 
                                type="text" 
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200" 
                                v-model="barcode" 
                                @keyup.prevent="getProductByBarcode" 
                                placeholder="Scannez votre code-barres" 
                                aria-label="Code-barres"
                            />
                            <i class="fa fa-barcode absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Tableau des produits avec scroller -->
                        <div class="overflow-y-auto bg-gray-50 rounded-lg shadow-md mb-6" style="max-height: 300px;">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-indigo-600 text-white">
                                    <tr>
                                        <th class="py-3 px-4 text-left">{{ trans('core.product') }}</th>
                                        <th class="py-3 px-4 text-center">{{ trans('core.quantity') }}</th>
                                        <th class="py-3 px-4 text-right">{{ trans('core.unit_price') }}</th>
                                        <th class="py-3 px-4 text-right">{{ trans('core.sub_total') }}</th>
                                        <th class="py-3 px-4 text-center">
                                            <i class="fa fa-trash"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="product in selectedProducts" :key="product.uuid" class="hover:bg-indigo-50">
                                        <td class="py-3 px-4">@{{ product.name }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button 
                                                type="button" 
                                                class="px-2 py-1 bg-gray-200 rounded-lg hover:bg-gray-300" 
                                                @click="decrementQuantity(product)"
                                                aria-label="Diminuer la quantité"
                                                >
                                                -
                                                </button>
                                                <span class="px-2">@{{ product.sell_quantity }}</span>
                                                <button 
                                                type="button" 
                                                class="px-2 py-1 bg-gray-200 rounded-lg hover:bg-gray-300" 
                                                @click="addQuantity(product)"
                                                aria-label="Augmenter la quantité"
                                                >
                                                +
                                                </button>
                                            </div>
                                            </td>
                                        <td class="py-3 px-4 text-right">@{{ product.mrp }}</td>
                                        <td class="py-3 px-4 text-right">@{{ (product.mrp * product.sell_quantity) }}</td>
                                        <td class="py-3 px-4 text-center text-red-500 cursor-pointer" @click.prevent="removeFromSelected(product)">
                                            <i class="fa fa-times"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Résumé simplifié -->
                        <div class="p-8 bg-white rounded-xl shadow-xl space-y-8">
                            <!-- Total, Discount, Total Payable -->
                            <div class="flex justify-between items-center text-gray-900 font-semibold">
                                <span class="text-xl">{{ trans('core.total') }}:</span>
                                <span class="text-2xl font-extrabold">@{{ subTotal | currency }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-900 font-semibold">
                                <span class="text-xl">{{ trans('core.discount') }}:</span>
                                <div class="flex items-center gap-3">
                                    <div class="relative w-36">
                                        <input 
                                            type="number" 
                                            v-model="discount" 
                                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" 
                                            placeholder="0.00" 
                                        />
                                        <span class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">CFA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-gray-900 font-semibold">
                                <span class="text-xl">{{ trans('core.total_payable') }}:</span>
                                <span class="text-2xl font-extrabold text-indigo-600">@{{ netTotal | currency }}</span>
                            </div>

                            <!-- Montant dû et payé -->
                            <div class="flex justify-between items-center text-gray-900 font-semibold mt-6">
                                <span class="text-xl">{{ trans('core.amount_due') }}:</span>
                                <span class="text-2xl font-extrabold text-red-500">@{{ (netTotal - paid) >= 0 ? (netTotal - paid | currency) : '0' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-900 font-semibold">
                                <span class="text-xl">{{ trans('core.amount_paid') }}:</span>
                                <div class="relative w-36">
                                    <input 
                                        type="number" 
                                        v-model="paid" 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" 
                                        placeholder="0.00" 
                                    />
                                    <span class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">CFA</span>
                                </div>
                            </div>

                            <!-- Change Amount -->
                            <div class="flex justify-between items-center text-gray-900 font-semibold mt-6">
                                <span class="text-xl">{{ trans('core.change') }}:</span>
                                <span class="text-2xl font-extrabold text-green-500">@{{ (paid - netTotal) >= 0 ? (paid - netTotal | currency) : '0' }}</span>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="mt-8 flex justify-between gap-4">
                                <button 
                                    type="button" 
                                    class="w-full py-4 bg-indigo-600 text-white rounded-lg text-xl font-medium hover:bg-indigo-700 transition duration-200 focus:ring-4 focus:ring-indigo-200"
                                    data-toggle="modal" 
                                    data-target="#paymentModal" 
                                    aria-label="Paiement"
                                >
                                    {{ trans('core.payment') }}
                                </button>
                            </div>
                        </div>

                    </form>
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

    <div id="loader_page" style="display: flex; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: white; z-index: 9999;">
        <div class="loader_page"></div>
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

                        //check if tax type is percentage(1) or fixed (2)
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
                addQuantity: function(product) {
                    product.sell_quantity += 1;
                },
                decrementQuantity: function(product) {
                    if (product.sell_quantity > 0) {
                        product.sell_quantity -= 1;
                    }
                },
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
                    axios.post('/api/admin/customer/save', this.addCustomer)
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
                    axios.get('/api/admin/client').then(function (response) {
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
                                '/api/admin/products' :
                                '/api/admin/category/' + data + '/products'
                    axios.get(getUrl)
                        .then(function (response) {
                            self.products = response.data
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
                    var searchUrl = '/api/admin/product-by-search/' + self.search
                    axios.get(searchUrl)
                        .then(function (response) {
                            self.products = response.data
                            self.loading = false
                        })
                        .catch(function (response) {
                            console.log(JSON.stringify(response))
                    })
                },

                getProductByBarcode: _.debounce(function () {
                    var self = this
                    axios.get('/api/admin/product-by-barcode/' + self.barcode)
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
                document.getElementById("loader_page").style.display = "none";
                document.getElementById("app").style.display = "block";
                this.loadClients()
                this.loadProducts('all')
            }
        });
    </script>
@stop
