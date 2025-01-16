// Separation of concerns with API services, error handling, and loading states
@parent
<script src="/assets/js-core/lodash.js"></script>
<script src="/assets/js-core/vue.js"></script>
<script src="/assets/js-core/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Axios global error handling
    axios.interceptors.response.use(
        response => response,
        error => {
            swal("Error", error.response?.data?.message || "Something went wrong", "error");
            return Promise.reject(error);
        }
    );

    // Centralized API service for clean separation
    const ApiService = {
        getClients() {
            return axios.get('/api/client');
        },
        getProducts(category) {
            const url = category === 'all' ? '/api/v1/products' : `/api/v1/category/${category}/products`;
            return axios.get(url);
        },
        searchProduct(query) {
            return axios.get(`/api/v1/product-by-search/${query}`);
        },
        getProductByBarcode(barcode) {
            return axios.get(`/api/v1/product-by-barcode/${barcode}`);
        },
        saveCustomer(customerData) {
            return axios.post('/api/v1/customer/save', customerData);
        },
        postSell(sellData) {
            return axios.post('/admin/pos/sell/save', sellData);
        }
    };

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
            totalQuantity() {
                return _.reduce(this.selectedProducts, (result, product) => result + parseInt(product.sell_quantity), 0);
            },
            subTotal() {
                const subtotal = _.reduce(this.selectedProducts, (result, product) => result + parseFloat(product.mrp) * parseFloat(product.sell_quantity), 0);
                return subtotal.toFixed(2);
            },
            discountAmount() {
                let discountAmount = this.discount;
                const isPercentage = this.discount.toString().includes('%');
                if (isPercentage) {
                    const amount = parseFloat(discountAmount.replace('%', ''));
                    discountAmount = this.subTotal * (amount / 100);
                }
                return discountAmount;
            },
            invoiceTax() {
                if (this.enableInvoiceTax) {
                    return this.invoice_tax_type === 1
                        ? (this.invoice_tax_rate * (this.subTotal - this.discountAmount)) / 100
                        : this.invoice_tax_rate;
                }
                return 0;
            },
            netTotal() {
                return parseFloat(this.subTotal - this.discountAmount + this.invoiceTax).toFixed(2);
            }
        },
        methods: {
            addQuantity(product) {
                const quantityToAdd = parseInt(product.sell_quantity);
                this.addToSelected(product, quantityToAdd, true);
            },
            resetClient() {
                this.addCustomer = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    address: '',
                    company_name: '',
                    client_type: 'retailer'
                };
            },
            postNewCustomer() {
                if (!this.addCustomer.first_name || !this.addCustomer.last_name) {
                    swal("Error", "First and Last names are required", "error");
                    return;
                }
                ApiService.saveCustomer(this.addCustomer)
                    .then(() => {
                        this.loadClients();
                        this.resetClient();
                        $(this.$refs.customerModalClose).trigger('click');
                    });
            },
            loadClients() {
                this.loading = true;
                ApiService.getClients()
                    .then(response => {
                        this.customers = response.data;
                        this.loading = false;
                    });
            },
            loadProducts(category = 'all') {
                this.loading = true;
                ApiService.getProducts(category)
                    .then(response => {
                        this.products = response.data.data;
                        this.loading = false;
                    });
            },
            getProductBySearch() {
                this.loading = true;
                ApiService.searchProduct(this.search)
                    .then(response => {
                        this.products = response.data.data;
                        this.loading = false;
                    });
            },
            getProductByBarcode: _.debounce(function () {
                ApiService.getProductByBarcode(this.barcode)
                    .then(response => {
                        if (response.data.found) {
                            this.addToSelected(response.data.product);
                            this.barcode = '';
                        }
                    });
            }, 300),
            addToSelected(product, quantityToAdd = 1, fresh = false) {
                if (fresh) product.sell_quantity = 0;
                if (this.selectedProducts[product.id]) {
                    product.sell_quantity = parseInt(this.selectedProducts[product.id].sell_quantity) + quantityToAdd;
                    this.$set(this.selectedProducts, product.id, product);
                } else {
                    product.sell_quantity = 1;
                    this.$set(this.selectedProducts, product.id, product);
                }
            },
            postSell() {
                if (this.totalQuantity <= 0) {
                    swal("Sorry", "Please select a product before payment", "warning");
                    return;
                }
                if (parseFloat(this.paid) < this.netTotal) {
                    swal("Sorry", `Paid amount can't be less than Net Total (${this.netTotal})`, "warning");
                    return;
                }
                const sellData = {
                    customer: this.customer,
                    sells: this.selectedProducts,
                    paid: this.paid,
                    method: this.paying_method,
                    discount_amount: this.discountAmount,
                    invoice_tax: this.invoiceTax
                };
                ApiService.postSell(sellData)
                    .then(response => {
                        swal("Success", "Transaction completed", "success");
                        window.location.href = `/pos/sell/invoice/${response.data.id}`;
                    });
            }
        },
        mounted() {
            this.loadClients();
            this.loadProducts('all');
        }
    });
</script>