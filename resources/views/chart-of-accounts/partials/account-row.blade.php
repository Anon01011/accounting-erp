<tr class="{{ $level > 0 ? 'hidden' : '' }}" data-parent="{{ $account->parent_id }}">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            @if($account->children->count() > 0)
                <button onclick="toggleChildren({{ $account->id }})" 
                        class="mr-2 text-gray-500 hover:text-gray-700 transition-transform duration-200"
                        data-toggle="{{ $account->id }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <span class="w-4 mr-2"></span>
            @endif
            <span class="text-sm text-gray-900">{{ $account->full_account_code }}</span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900" style="padding-left: {{ $level * 1.5 }}rem">
            {{ $account->name }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ optional($account->type)->name }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ optional($account->group)->name }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ optional($account->class)->name }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $account->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex space-x-2">
            <a href="{{ route('chart-of-accounts.edit', $account) }}" 
               class="text-indigo-600 hover:text-indigo-900">
                Edit
            </a>
            <form action="{{ route('chart-of-accounts.destroy', $account) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this account?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900">
                    Delete
                </button>
            </form>
        </div>
    </td>
</tr>

@foreach($account->children as $child)
    @include('chart-of-accounts.partials.account-row', ['account' => $child, 'level' => $level + 1])
@endforeach 