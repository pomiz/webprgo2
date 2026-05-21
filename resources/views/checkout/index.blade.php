@extends('layouts.user')
@section('title', 'Checkout - Ruang Baju')

@section('content')
    <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Address & Shipping --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Address Selection --}}
            <div class="card p-6">
                <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">Alamat Pengiriman</h2>

                <div x-data="shippingCalculator()" x-init="init()">
                    {{-- Method Toggle --}}
                    <div class="flex gap-3 mb-4">
                        <button type="button" @click="method = 'dropdown'" :class="method === 'dropdown' ? 'btn-primary' : 'btn-outline'" class="text-sm !py-2 !px-4">
                            Pilih Kota
                        </button>
                        <button type="button" @click="method = 'gps'; getLocation()" :class="method === 'gps' ? 'btn-primary' : 'btn-outline'" class="text-sm !py-2 !px-4">
                            Gunakan Lokasi Saya
                        </button>
                    </div>

                    {{-- Dropdown Method --}}
                    <div x-show="method === 'dropdown'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provinsi</label>
                            <select x-model="province" @change="fetchCities()" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-800 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500">
                                <option value="">Pilih Provinsi</option>
                                <template x-for="p in provinces" :key="p">
                                    <option :value="p" x-text="p"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota</label>
                            <select x-model="city" @change="selectCity()" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-800 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500">
                                <option value="">Pilih Kota</option>
                                <template x-for="c in cities" :key="c.city">
                                    <option :value="c.city" x-text="c.city"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- GPS Method --}}
                    <div x-show="method === 'gps'" class="space-y-2">
                        <div x-show="gpsLoading" class="text-sm text-gray-500 dark:text-gray-400">
                            <svg class="animate-spin inline w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Mendapatkan lokasi...
                        </div>
                        <div x-show="gpsError" class="text-sm text-red-500" x-text="gpsError"></div>
                        <div x-show="latitude && method === 'gps'" class="text-sm text-green-600 dark:text-green-400">
                            Lokasi berhasil didapatkan
                        </div>
                    </div>

                    {{-- Shipping Result --}}
                    <div x-show="shippingCost !== null" class="mt-4 p-4 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-800 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-brand-800 dark:text-brand-300">Ongkos Kirim</p>
                                <p class="text-xs text-brand-600 dark:text-brand-400" x-text="'Jarak: ' + distance + ' km'"></p>
                            </div>
                            <p class="text-lg font-bold text-brand-900 dark:text-brand-200" x-text="'Rp ' + Number(shippingCost).toLocaleString('id-ID')"></p>
                        </div>
                    </div>

                    <div x-show="shippingError" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400" x-text="shippingError"></p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="card p-6">
                <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">Produk</h2>
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-surface-800 flex-shrink-0">
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/48?text=No';">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }}x Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-24" x-data="checkoutSummary()">
                <h3 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan</h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Ongkir</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="shippingDisplay()">-</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mb-6">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="font-bold text-lg text-gray-900 dark:text-white" x-text="totalDisplay()">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="shipping_cost" :value="getShippingCost()">
                    <input type="hidden" name="shipping_address" :value="getShippingAddress()">
                    <input type="hidden" name="latitude" :value="getLatitude()">
                    <input type="hidden" name="longitude" :value="getLongitude()">
                    <input type="hidden" name="province" :value="getProvince()">
                    <input type="hidden" name="city" :value="getCity()">

                    <button type="submit" class="btn-primary w-full text-center" :disabled="!canCheckout()" :class="{ 'opacity-50 cursor-not-allowed': !canCheckout() }">
                        Konfirmasi Pesanan
                    </button>
                </form>

                <p x-show="!canCheckout()" class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">Pilih alamat pengiriman terlebih dahulu</p>
            </div>
        </div>
    </div>

    <script>
        function shippingCalculator() {
            return {
                method: 'dropdown',
                provinces: [],
                cities: [],
                province: '{{ $defaultAddress?->province ?? "" }}',
                city: '{{ $defaultAddress?->city ?? "" }}',
                latitude: {{ $defaultAddress?->latitude ?? 'null' }},
                longitude: {{ $defaultAddress?->longitude ?? 'null' }},
                shippingCost: null,
                distance: null,
                shippingError: null,
                gpsLoading: false,
                gpsError: null,

                init() {
                    fetch('{{ route("shipping.provinces") }}')
                        .then(r => r.json())
                        .then(data => {
                            this.provinces = data;
                            if (this.province) this.fetchCities().then(() => {
                                if (this.latitude && this.longitude) this.calculateShipping();
                            });
                        });
                },

                fetchCities() {
                    if (!this.province) { this.cities = []; return Promise.resolve(); }
                    return fetch('{{ route("shipping.cities") }}?province=' + encodeURIComponent(this.province))
                        .then(r => r.json())
                        .then(data => { this.cities = data; });
                },

                selectCity() {
                    const selected = this.cities.find(c => c.city === this.city);
                    if (selected) {
                        this.latitude = selected.latitude;
                        this.longitude = selected.longitude;
                        this.calculateShipping();
                    }
                },

                getLocation() {
                    this.gpsLoading = true;
                    this.gpsError = null;
                    if (!navigator.geolocation) {
                        this.gpsError = 'Browser tidak mendukung geolocation.';
                        this.gpsLoading = false;
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.latitude = pos.coords.latitude;
                            this.longitude = pos.coords.longitude;
                            this.gpsLoading = false;
                            this.calculateShipping();
                        },
                        (err) => {
                            this.gpsError = 'Gagal mendapatkan lokasi: ' + err.message;
                            this.gpsLoading = false;
                        }
                    );
                },

                calculateShipping() {
                    if (!this.latitude || !this.longitude) return;
                    this.shippingError = null;
                    fetch('{{ route("shipping.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ latitude: this.latitude, longitude: this.longitude })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            this.shippingError = data.error;
                            this.shippingCost = null;
                        } else {
                            this.shippingCost = data.cost;
                            this.distance = data.distance_km;
                        }
                        window.dispatchEvent(new CustomEvent('shipping-updated', { detail: { cost: this.shippingCost, lat: this.latitude, lng: this.longitude, province: this.province, city: this.city } }));
                    });
                }
            };
        }

        function checkoutSummary() {
            return {
                shipping: null,
                lat: null,
                lng: null,
                prov: null,
                cty: null,
                subtotal: {{ $subtotal }},

                init() {
                    window.addEventListener('shipping-updated', (e) => {
                        this.shipping = e.detail.cost;
                        this.lat = e.detail.lat;
                        this.lng = e.detail.lng;
                        this.prov = e.detail.province;
                        this.cty = e.detail.city;
                    });
                },

                shippingDisplay() {
                    return this.shipping !== null ? 'Rp ' + Number(this.shipping).toLocaleString('id-ID') : '-';
                },
                totalDisplay() {
                    const total = this.shipping !== null ? this.subtotal + this.shipping : this.subtotal;
                    return 'Rp ' + Number(total).toLocaleString('id-ID');
                },
                canCheckout() { return this.shipping !== null && this.lat !== null; },
                getShippingCost() { return this.shipping ?? 0; },
                getShippingAddress() { return (this.prov ? this.prov + ', ' : '') + (this.cty ?? ''); },
                getLatitude() { return this.lat; },
                getLongitude() { return this.lng; },
                getProvince() { return this.prov; },
                getCity() { return this.cty; },
            };
        }
    </script>
@endsection
