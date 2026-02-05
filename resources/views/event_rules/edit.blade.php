<x-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Edit Rule: {{ $eventRule->event_category }}</h1>

        <div class="bg-white shadow-md rounded-lg p-6 max-w-lg">
            <form action="{{ route('event-rules.update', $eventRule->event_category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Category is PK, usually uneditable or needs special care. Let's keep it read-only here -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="event_category_display">
                        Category Name
                    </label>
                    <input type="text" value="{{ $eventRule->event_category }}" readonly disabled
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">Category name cannot be changed once created.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="required_skills">
                        Required Skills (Comma Separated)
                    </label>
                    <input type="text" name="required_skills" id="required_skills" 
                        value="{{ old('required_skills', is_array($eventRule->required_skills) ? implode(', ', $eventRule->required_skills) : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="required_specialization">
                        Required Specialization
                    </label>
                    <select name="required_specialization" id="required_specialization"
                         class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                         <option value="">-- None --</option>
                         @foreach(['Outdoor Activities', 'Corporate Training', 'Education', 'Motivation', 'Recreation', 'Culinary Arts'] as $spec)
                            <option value="{{ $spec }}" {{ $eventRule->required_specialization == $spec ? 'selected' : '' }}>
                                {{ $spec }}
                            </option>
                         @endforeach
                    </select>
                </div>

                <div class="flex -mx-2">
                    <div class="w-1/2 px-2 mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min_experience">
                            Min Experience (Years)
                        </label>
                        <input type="number" name="min_experience" id="min_experience" 
                            value="{{ old('min_experience', $eventRule->min_experience) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="0">
                    </div>
                    <div class="w-1/2 px-2 mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min_rating">
                            Min Rating (0-5)
                        </label>
                        <input type="number" name="min_rating" id="min_rating" 
                            value="{{ old('min_rating', $eventRule->min_rating) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="0" max="5">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Rule
                    </button>
                    <a href="{{ route('event-rules.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
