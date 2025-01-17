<div class="w-full text-gray-700">
    <div x-data="{ open: false }" x-cloak
         class="flex flex-col max-w-screen-xl px-4 mx-auto md:items-center md:justify-between md:flex-row md:px-6 lg:px-8">
        <div class="p-4 flex flex-row items-center justify-between">
            <a href="{{ route('home') }}"
               class="text-lg font-semibold tracking-widest text-gray-900 uppercase rounded-lg focus:outline-none focus:shadow-outline">
                <img class="block h-5 pointer-events-none" src="{{ asset('images/logos/white.svg') }}"
                     alt="Guldenbook logo"/>
            </a>
            <button class="md:hidden rounded-lg focus:outline-none focus:shadow-outline" @click="open = !open">
                <svg fill="white" viewBox="0 0 20 20" class="w-6 h-6">
                    <path x-show="!open" fill-rule="evenodd"
                          d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                          clip-rule="evenodd"></path>
                    <path x-show="open" fill-rule="evenodd"
                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                          clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        <nav :class="{hidden: !open}"
             class="flex-col flex-grow pb-4 gap-x-2 md:pb-0 hidden md:flex md:justify-end md:flex-row">
            <x-navigation.nav-item route="price">
                Price
            </x-navigation.nav-item>
            <x-navigation.nav-group name="Network">
                <x-navigation.nav-group-item route="node-information" show-testnet>
                    Node information
                </x-navigation.nav-group-item>
                <x-navigation.nav-group-item route="nonce-distribution" show-testnet>
                    Nonce distribution
                </x-navigation.nav-group-item>
                <x-navigation.nav-group-item route="average-blocktime" show-testnet>
                    Average blocktime
                </x-navigation.nav-group-item>
            </x-navigation.nav-group>
            <x-navigation.nav-group name="Calculators">
                <x-navigation.nav-group-item route="calculator.mining">
                    Mining
                </x-navigation.nav-group-item>
                <x-navigation.nav-group-item route="calculator.witness">
                    Witness
                </x-navigation.nav-group-item>
            </x-navigation.nav-group>
            <x-navigation.nav-item route="https://forms.gle/ynxdGLypD4o6YgHE9" target="_blank">
                Missing something?
            </x-navigation.nav-item>
        </nav>
    </div>
</div>
