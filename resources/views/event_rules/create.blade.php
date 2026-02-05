<x-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Create New Event Rule</h1>

        <div class="bg-white shadow-md rounded-lg p-6 max-w-lg">
            <form action="{{ route('event-rules.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="event_category">
                        Category Name (Unique)
                    </label>
                    <input type="text" name="event_category" id="event_category" value="{{ old('event_category') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        required placeholder="e.g. SURVIVAL CAMP">
                    @error('event_category')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="required_skills">
                        Required Skills (Comma Separated)
                    </label>
                    <input type="text" name="required_skills" id="required_skills" value="{{ old('required_skills') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="e.g. Medic, Swimming, Leadership">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="required_specialization">
                        Required Specialization (Bonus Match)
                    </label>
                    <select name="required_specialization" id="required_specialization"
                         class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                         <option value="">-- None --</option>
                         <option value="Outdoor Activities">Outdoor Activities</option>
                         <option value="Corporate Training">Corporate Training</option>
                         <option value="Education">Education</option>
                         <option value="Motivation">Motivation</option>
                         <option value="Recreation">Recreation</option>
                         <option value="Culinary Arts">Culinary Arts</option>
                    </select>
                </div>

                <div class="flex -mx-2">
                    <div class="w-1/2 px-2 mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min_experience">
                            Min Experience (Years)
                        </label>
                        <input type="number" name="min_experience" id="min_experience" value="{{ old('min_experience', 0) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="0">
                    </div>
                    <div class="w-1/2 px-2 mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min_rating">
                            Min Rating (0-5)
                        </label>
                        <input type="number" name="min_rating" id="min_rating" value="{{ old('min_rating', 0) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="0" max="5">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Rule
                    </button>
                    <a href="{{ route('event-rules.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
