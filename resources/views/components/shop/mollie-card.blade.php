<div>
    <form id="mollie-card-form">
        <x-blocks.card>
            <div class="flex items-center justify-between">
                <div>
                    <x-label class="block mb-2">Token</x-label>
                    <x-typography.title id="mollie-card-token">test</x-typography.title>
                </div>
                <x-buttons.button
                    id="mollie-card-button"
                    color="blue"
                >Generate</x-buttons.button>
            </div>
        </x-blocks.card>
        <div id="mollie-card"></div>
    </form>
    <script>
        const mollie = Mollie('{{ $data['profile_id'] }}', {
            locale: '{{ $data['locale'] }}',
            testmode: {{ $data['testmode'] }}
        });

        const cardComponent = mollie.createComponent('card');
        cardComponent.mount('#mollie-card');

        const element = document.getElementById('mollie-card-token')

        const button = document.getElementById('mollie-card-button')
        button.addEventListener('click', async e => {
            var { token, error } = await mollie.createToken();

            if (error) {
                // Something wrong happened while creating the token. Handle this situation gracefully.
                return;
            }

            element.innerHTML = token

            @this.dispatch('cart.resource-data', { key: 'card_token', value: token });
        })
    </script>
</div>
<x-grid-2>
    <x-blocks.card>
        <x-label class="block mb-2">American Express</x-label>
        <x-typography.title>3782 822463 10005</x-typography.title>
    </x-blocks.card>
    <x-blocks.card>
        <x-label class="block mb-2">Mastercard</x-label>
        <x-typography.title>2223 0000 1047 9399</x-typography.title>
    </x-blocks.card>
    <x-blocks.card>
        <x-label class="block mb-2">VISA</x-label>
        <x-typography.title>4543 4740 0224 9996</x-typography.title>
    </x-blocks.card>
</x-grid-2>
<style>
    #mollie-card {
        padding: 1rem;
        border: 1px solid #eee;
        border-radius: 10px;
    }
    .mollie-card-component {
        padding-bottom: 1rem;
    }
    .mollie-card-component__error {
        color: red;
    }
    .mollie-component--cardHolder,
    .mollie-component--expiryDate,
    .mollie-component--verificationCode,
    .mollie-component--cardNumber {
        margin: 0.5rem 0;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 1rem;
    }
</style>
