<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-lg shadow-xl">
            <div class="modal-header bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-lg p-4">
                <h5 class="modal-title text-xl font-bold" id="paymentModalLabel">
                    <i class="fa fa-shopping-cart mr-3"></i>Finaliser la Vente
                </h5>
                <button type="button" class="close text-white opacity-75 hover:opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Payment Details Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Montant Payé</label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    v-model="paid" 
                                    class="w-full h-12 px-4 pr-12 text-lg border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                    placeholder="Entrer le montant"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">CFA</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Méthode de Paiement</label>
                            <select 
                                v-model="paying_method" 
                                class="w-full h-12 px-4 text-lg border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            >
                                <option value="cash">Espèces</option>
                                <option value="card">Carte</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transaction Summary Column -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-4">
                        <div class="text-center">
                            <h5 class="text-xl font-bold text-gray-800 mb-4">Résumé de la Transaction</h5>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-semibold">Total Articles</span>
                            <span class="text-gray-900 font-bold">@{{ totalQuantity }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-semibold">Total à Payer</span>
                            <span class="text-blue-600 font-bold text-lg">@{{ netTotal }} CFA</span>
                        </div>

                        <div class="flex justify-between items-center text-green-600">
                            <span class="font-semibold">Monnaie</span>
                            <span class="font-bold">@{{ paid - netTotal > 0 ? (paid - netTotal).toFixed(2) : 0 }} CFA</span>
                        </div>

                        <div class="flex justify-between items-center text-red-600">
                            <span class="font-semibold">Reste à Payer</span>
                            <span class="font-bold">@{{ netTotal - paid > 0 ? (netTotal - paid).toFixed(2) : 0 }} CFA</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <div class="mt-6">
                    <button 
                        @click.prevent="postSell"
                        :disabled="isSubmitting"
                        class="w-full h-14 bg-blue-600 text-white text-lg font-bold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all disabled:opacity-50"
                    >
                        <i class="fa fa-check-circle mr-3"></i>Valider le Paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>