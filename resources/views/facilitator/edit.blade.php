@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-slate-800">Edit Profile</h2>

    <form action="{{ route('facilitator.update') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700">Skills (Space/Comma separated)</label>
            <input type="text" name="skills" value="{{ $facilitator->skills }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
            <p class="text-xs text-gray-500 mt-1">Keywords used for matchmaking.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Experience (Keywords)</label>
            <textarea name="experience" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">{{ $facilitator->experience }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Certifications</label>
            <textarea name="certifications" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">{{ $facilitator->certifications }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
             <div>
                <label class="block text-sm font-medium text-slate-700">Bank Name</label>
                <input type="text" name="bank_name" value="{{ $facilitator->bank_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
            </div>
             <div>
                <label class="block text-sm font-medium text-slate-700">Account Number</label>
                <input type="text" name="bank_account_number" value="{{ $facilitator->bank_account_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Phone</label>
            <input type="text" name="phone_number" value="{{ $facilitator->phone_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update Profile</button>
        </div>
    </form>
</div>
@endsection
