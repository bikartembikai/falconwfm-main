@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Payment Management</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all facilitator payments, their bank details, and status.</p>
        </div>
    </div>
    
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Facilitator</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Event</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Bank Details</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($payments as $payment)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $payment->attendance->facilitator->user->name ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $payment->attendance->event->event_name ?? 'N/A' }}
                                    <div class="text-xs text-gray-400">{{ $payment->attendance->event->start_date_time->format('d M Y') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="font-bold text-gray-700">{{ $payment->attendance->facilitator->bank_name ?? 'No Bank' }}</div>
                                    <div class="font-mono text-gray-600">{{ $payment->attendance->facilitator->bank_account_number ?? '-' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                    RM {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($payment->payment_status === 'paid')
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Paid</span>
                                        <div class="text-xs text-gray-500 mt-1">{{ optional($payment->payment_date)->format('d M') }}</div>
                                    @else
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Pending</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if($payment->payment_status !== 'paid')
                                        <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="file" name="payment_proof" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                            <button type="submit" class="inline-flex justify-center items-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Confirm Paid
                                            </button>
                                        </form>
                                    @else
                                        @if($payment->payment_proof)
                                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">View Proof</a>
                                        @else
                                            <span class="text-gray-400">No Proof</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
