@php
    $hasChildren = !empty($node['children']);
@endphp

<li style="margin-left: {{ $depth > 0 ? '1rem' : '0' }};">
    @if($hasChildren)
        <details class="group" open>
            <summary class="cursor-pointer text-sm text-gray-800 dark:text-gray-200">
                {{ $node['name'] }}
            </summary>
            <ul class="mt-1 space-y-1 border-l border-gray-300 pl-3 dark:border-gray-700">
                @foreach($node['children'] as $child)
                    @include('products.partials.category-tree-node', ['node' => $child, 'depth' => $depth + 1])
                @endforeach
            </ul>
        </details>
    @else
        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $node['name'] }}</span>
    @endif
</li>
