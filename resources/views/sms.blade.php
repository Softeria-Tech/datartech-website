<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk sms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4>Open Bulk Sms Gateway</h4><br>
                    <a href="https://sms.softeriatech.com/partner/auth/f05dd6a084fbbd792c9eb9e8ca79d67bb7113b3cca0c2f656dd44ea957e75714" target="_blank" style="color:blue; text-decoration: underline;">https://sms.softeriatech.com</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.open('https://sms.softeriatech.com/partner/auth/f05dd6a084fbbd792c9eb9e8ca79d67bb7113b3cca0c2f656dd44ea957e75714', '_blank');
        });
    </script>
</x-admin-layout>

