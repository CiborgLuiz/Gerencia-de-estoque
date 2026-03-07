@php
    $hasChildren = !empty($node['children']);
@endphp

<li style="margin-left: {{ $depth > 0 ? '1rem' : '0' }};" class="group relative pr-14">
    <form
        method="POST"
        action="{{ route('categories.destroy', $node['id']) }}"
        class="absolute right-0 top-0 opacity-0 transition-opacity group-hover:opacity-100"
        onsubmit="return confirm('Deseja apagar a categoria {{ addslashes($node['name']) }}?')"
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded px-2 py-0.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
            Apagar
        </button>
    </form>

    @if($hasChildren)
        <details class="group" open>
            <summary class="cursor-pointer text-sm text-gray-800 dark:text-gray-200 pr-2">
                {{ $node['name'] }}
            </summary>
            <ul class="mt-1 space-y-1 border-l border-gray-300 pl-3 dark:border-gray-700">
                @foreach($node['children'] as $child)
                    @include('products.partials.category-tree-node', ['node' => $child, 'depth' => $depth + 1])
                @endforeach
            </ul>
        </details>
    @else
        <span class="text-sm text-gray-700 dark:text-gray-300 pr-2">{{ $node['name'] }}</span>
    @endif
</li>
