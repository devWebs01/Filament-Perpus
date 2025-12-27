<?php

use function Livewire\Volt\{state};
use App\Models\Status;

state([
    'rules' => fn() => Status::where('amount', '>', 0)->get(),
]);

?>

@volt
    <div>

        <div class="flex items-start">
            <i class="iconoir-money-square
 w-5 h-5 text-gray-400 text-xl mr-3 mt-0.5"></i>
            <div>
                <p class="text-gray-900 text-sm font-semibold">Denda
                    Peminjaman</p>
                @foreach ($rules as $rule)
                    <p class="text-sm text-gray-600">
                        {{ $rule->name . ' - ' . formatRupiah($rule->amount) }}
                    </p>
                @endforeach
            </div>
        </div>
    </div>
@endvolt
