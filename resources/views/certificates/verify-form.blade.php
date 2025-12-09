<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900">Verifikasi Sertifikat</h1>
                <p class="text-gray-600 mt-2">Masukkan kode verifikasi untuk memverifikasi keaslian sertifikat</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('certificates.verify.post') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Verifikasi
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Masukkan kode verifikasi sertifikat">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verifikasi Sertifikat
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500 text-center">
                            Kode verifikasi dapat ditemukan di sertifikat PDF atau diberikan oleh pemegang sertifikat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

