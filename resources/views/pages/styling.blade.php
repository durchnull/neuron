@php
    $color = 'gray-300'
@endphp
<x-layouts.web>

    <x-blocks.section class="mt-16 pt-4">
        <ul class="flex flex-wrap">
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Typography">Typography</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Html">Html</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Blocks">Blocks</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Buttons">Buttons</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Form">Form</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Image">Image</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Navigation">Navigation</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Parsing">Parsing</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Shapes">Shapes</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Misc">Misc</x-navigation.anchor></li>
            <li class="bg-white m-1 p-2 rounded border-b"><x-navigation.anchor href="#Icons">Icons</x-navigation.anchor></li>
        </ul>
    </x-blocks.section>
    <x-blocks.section class="mt-16 pt-4" id="Typography">
        <x-typography.headline class="text-{{ $color }}"># Typography</x-typography.headline>
        <x-styling.block>
            <x-typography.headline>Headline</x-typography.headline>
        </x-styling.block>
        <x-styling.block>
            <x-typography.title>Title</x-typography.title>
        </x-styling.block>
        <x-styling.block>
            <x-typography.small>Small</x-typography.small>
        </x-styling.block>
        <x-styling.block>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore dolores dolorum esse et, expedita hic illum libero, natus omnis perspiciatis provident quaerat reprehenderit similique soluta sunt, totam ullam? Eos, inventore.</p>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Html">
        <x-typography.headline class="text-{{ $color }}"># Html</x-typography.headline>
        <x-styling.block>
            <x-html.pre>Pre</x-html.pre>
        </x-styling.block>
        <x-styling.block>
            <x-html.table>
                <thead>
                    <x-html.th>Th 1</x-html.th>
                    <x-html.th>Th 2</x-html.th>
                    <x-html.th>Th 3</x-html.th>
                    <x-html.th>Th 4</x-html.th>
                </thead>
                <tr>
                    <x-html.td>Td 1</x-html.td>
                    <x-html.td>Td 2</x-html.td>
                    <x-html.td>Td 3</x-html.td>
                    <x-html.td>Td 4</x-html.td>
                </tr>
            </x-html.table>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Blocks">
        <x-typography.headline class="text-{{ $color }}"># Blocks</x-typography.headline>
        <x-blocks.section class="bg-{{ $color }}-100 p-4">Section</x-blocks.section>
        <x-blocks.card>Card</x-blocks.card>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Buttons">
        <x-typography.headline class="text-{{ $color }}"># Buttons</x-typography.headline>
        <x-styling.block title="Button">
            <x-buttons.button>Button</x-buttons.button>
        </x-styling.block>
        <x-styling.block title="Add">
            <x-buttons.button-add>Add</x-buttons.button-add>
        </x-styling.block>
        <x-styling.block title="Radio">
            <x-buttons.button-radio/>
        </x-styling.block>
        <x-styling.block title="Color">
            <x-buttons.button color="black">Black</x-buttons.button>
            <x-buttons.button color="blue">Blue</x-buttons.button>
            <x-buttons.button color="green">Green</x-buttons.button>
            <x-buttons.button color="red">Red</x-buttons.button>
            <x-buttons.button color="yellow">Yellow</x-buttons.button>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Form">
        <x-typography.headline class="text-{{ $color }}"># Form</x-typography.headline>
        <x-styling.block title="Quantity">
            <x-form.quantity productId="1" quantity="1"/>
        </x-styling.block>
        <x-styling.block title="Select">
            <x-form.select :options="[['value' => 'value-1', 'selected' => true, 'label' => 'Option 1']]"/>
        </x-styling.block>
        <x-styling.block title="Input Text">
            <x-form.text
                model="model_text"
                label="Text"
                placeholder="Text"
            />
        </x-styling.block>
        <x-styling.block title="Input Email">
            <x-form.email
                model="model_email"
                label="Email"
                placeholder="Email"
            />
        </x-styling.block>
        <x-styling.block title="Input Password">
            <x-form.password
                model="model_password"
                label="Password"
                placeholder="Password"
            />
        </x-styling.block>
        <x-styling.block title="Input Checkbox">
            <x-form.checkbox
                model="model_checkbox"
                label="Checkbox"
                placeholder="Checkbox"
            />
        </x-styling.block>
        <x-styling.block title="Input Checkbox (Toggle)">
            <x-form.toggle
                model="model_toggle"
                :value="true"
            />
            <x-form.toggle
                model="model_toggle"
                :value="false"
            />
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Image">
        <x-typography.headline class="text-{{ $color }}"># Image</x-typography.headline>
        <x-styling.block>
            <x-image.product-image name="bonbon"/>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Navigation">
        <x-typography.headline class="text-{{ $color }}"># Navigation</x-typography.headline>
        <x-styling.block title="Anchor">
            <x-navigation.anchor href="/styling">Anchor</x-navigation.anchor>
            <x-navigation.anchor href="/styling" icon="shopping-cart">Anchor</x-navigation.anchor>
        </x-styling.block>
        <x-styling.block title="Anchor button">
            <x-navigation.button-anchor
                href="/styling"
                icon="arrow-small-right"
            >Select a sales channel first</x-navigation.button-anchor>
        </x-styling.block>
        <x-styling.block title="Tabs">
            <x-navigation.tabs :tabs="['Step', 'Step']"
                                id="first"
            >
                <x-navigation.tab tab="1">
                    <span>Tab 1</span>
                </x-navigation.tab>
                <x-navigation.tab tab="1">
                    <span>Tab 2</span>
                </x-navigation.tab>
            </x-navigation.tabs>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Parsing">
        <x-typography.headline class="text-{{ $color }}"># Parsing</x-typography.headline>
        <x-styling.block title="Coupon Code">
            <x-parsing.coupon-code code="COUPON"/>
        </x-styling.block>
        <x-styling.block title="DateTime">
            <x-parsing.date-time :value="now()"/>
        </x-styling.block>
        <x-styling.block title="Price">
            <x-parsing.price amount="1299"/>
        </x-styling.block>
        <x-styling.block title="Conditions">
            {{-- @todo [styling] --}}
        </x-styling.block>
        <x-styling.block title="Consequences">
            {{-- @todo [styling] --}}
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Shapes">
        <x-typography.headline class="text-{{ $color }}"># Shapes</x-typography.headline>
        <x-styling.block title="Circle">
            <x-shapes.circle class="bg-blue-400 text-white">1</x-shapes.circle>
        </x-styling.block>
        <x-styling.block title="Dot">
            <x-shapes.dot class="bg-blue-400"/>
            <x-shapes.dot class="bg-red-400"/>
            <x-shapes.dot class="bg-{{ $color }}"/>
        </x-styling.block>
        <x-styling.block title="Pill">
            <x-shapes.pill>Pill</x-shapes.pill>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Misc">
        <x-typography.headline class="text-{{ $color }}"># Misc</x-typography.headline>
        <x-styling.block title="Label">
            <x-label>Label</x-label>
        </x-styling.block>
        <x-styling.block title="Loading">
            <x-loading/>
        </x-styling.block>
        <x-styling.block title="Spinner">
            <x-spinner/>
        </x-styling.block>
    </x-blocks.section>

    <x-blocks.section class="mt-16 pt-4" id="Icons">
        <x-typography.headline class="text-{{ $color }}"># Icons</x-typography.headline>
        <div class="grid grid-cols-5 gap-1">
            @foreach (glob(resource_path('views/svg/*.blade.php')) as $iconFile)
                @php
                    $icon = \Illuminate\Support\Str::before(\Illuminate\Support\Str::after($iconFile, 'views/svg/'), '.blade.php');
                @endphp
                <x-styling.block title="{{ $icon }}">
                    @include('svg.' . $icon)
                </x-styling.block>
            @endforeach
        </div>
    </x-blocks.section>

</x-layouts.web>
