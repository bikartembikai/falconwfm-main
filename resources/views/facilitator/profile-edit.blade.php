@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
        <p class="text-slate-500 mt-1">Manage your facilitator profile information</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit text-center">
            <div class="w-32 h-32 mx-auto bg-[#1a8a5f] rounded-full flex items-center justify-center text-4xl font-bold text-white mb-4">
                {{ substr($user->name, 0, 2) }}
            </div>
            
            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
            <p class="text-slate-500 text-sm mb-4">Facilitator</p>
            
            <div class="flex items-center justify-center space-x-1 text-yellow-500 mb-6">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span class="font-bold text-slate-900">{{ number_format($user->averageRating, 1) }}</span>
                <span class="text-slate-400 font-normal">Average Rating</span>
            </div>
            
            <div class="border-t border-slate-100 pt-6">
                <div class="flex items-center justify-center text-slate-600 text-sm">
                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    {{ $user->experience }} years experience
                </div>
            </div>
        </div>

        <!-- Right Column: Details Form -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('facilitator.updateProfile') }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Phone Number</label>
                            <div class="relative">
                                <input type="text" name="phoneNumber" value="{{ old('phoneNumber', $user->phoneNumber) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent" placeholder="+60">
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            @error('phoneNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Professional Information
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Skills</label>
                            
                            <!-- Custom Skill Selector -->
                            <div class="skill-selector w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-2 text-sm focus-within:ring-2 focus-within:ring-[#1a8a5f] focus-within:border-transparent flex flex-wrap gap-2 items-center relative">
                                <!-- Selected Skills (Chips) -->
                                <div id="selectedSelectsContainer" class="flex flex-wrap gap-2">
                                    @foreach($user->skills as $skill)
                                        <div class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded flex items-center">
                                            {{ $skill->skillName }}
                                            <button type="button" class="ml-1.5 text-emerald-600 hover:text-emerald-900 focus:outline-none" onclick="removeSkill(this, '{{ $skill->skillName }}')">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Input Field -->
                                <input type="text" id="skillInput" class="bg-transparent border-none focus:ring-0 p-0 text-sm flex-1 min-w-[120px]" placeholder="Add a skill..." autocomplete="off">
                                
                                <!-- Suggestions Dropdown -->
                                <ul id="skillSuggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg max-h-60 overflow-y-auto z-50 hidden">
                                    <!-- Suggestions will be populated here -->
                                </ul>
                            </div>
                            
                            <!-- Hidden Input for Form Submission -->
                            <input type="hidden" name="skills" id="hiddenSkillsInput" value="{{ $user->skills->pluck('skillName')->implode(',') }}">

                            <p class="text-xs text-slate-400 mt-1">Type to search or add new skills. Press Enter to add.</p>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const allSkills = @json($allSkills ?? []);
                                const skillInput = document.getElementById('skillInput');
                                const suggestionsList = document.getElementById('skillSuggestions');
                                const selectedContainer = document.getElementById('selectedSelectsContainer');
                                const hiddenInput = document.getElementById('hiddenSkillsInput');
                                
                                let currentSkills = hiddenInput.value ? hiddenInput.value.split(',').map(s => s.trim()).filter(s => s) : [];

                                function updateHiddenInput() {
                                    hiddenInput.value = currentSkills.join(',');
                                }

                                function addSkill(skillName) {
                                    skillName = skillName.trim();
                                    if (!skillName || currentSkills.includes(skillName)) return;

                                    currentSkills.push(skillName);
                                    updateHiddenInput();

                                    // Create UI Element
                                    const chip = document.createElement('div');
                                    chip.className = 'bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded flex items-center';
                                    chip.innerHTML = `
                                        ${skillName}
                                        <button type="button" class="ml-1.5 text-emerald-600 hover:text-emerald-900 focus:outline-none">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    `;
                                    
                                    // Add remove functionality
                                    chip.querySelector('button').addEventListener('click', function() {
                                        removeSkillInternal(skillName, chip);
                                    });

                                    selectedContainer.appendChild(chip);
                                    skillInput.value = '';
                                    suggestionsList.classList.add('hidden');
                                }

                                function removeSkillInternal(skillName, element) {
                                    currentSkills = currentSkills.filter(s => s !== skillName);
                                    updateHiddenInput();
                                    element.remove();
                                }
                                
                                // Make removeSkill globally available for initial items
                                window.removeSkill = function(btn, skillName) {
                                    const chip = btn.closest('div');
                                    removeSkillInternal(skillName, chip);
                                };

                                // Function to filter and show suggestions
                                function showSuggestions(query = '') {
                                    const filtered = allSkills.filter(skill => 
                                        skill.toLowerCase().includes(query.toLowerCase()) && !currentSkills.includes(skill)
                                    );

                                    suggestionsList.innerHTML = '';
                                    
                                    if (filtered.length > 0) {
                                        filtered.forEach(skill => {
                                            const li = document.createElement('li');
                                            li.className = 'px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm text-slate-700';
                                            li.textContent = skill;
                                            li.addEventListener('click', () => addSkill(skill));
                                            suggestionsList.appendChild(li);
                                        });
                                        suggestionsList.classList.remove('hidden');
                                    } else {
                                         // Show specific message if no matches, or valid "Create new" option could go here
                                         if (query.length > 0) { // Only hide if typing and no match? Or show "Create X"?
                                             // For now, let's keep it simple: if typing and no match, hide list (user can press enter)
                                             suggestionsList.classList.add('hidden');
                                         } else if (allSkills.length > 0) {
                                             // If query empty but we have skills (that aren't selected), show them?
                                             // The filter above handles !includes(skill), so if all selected, filtered is empty.
                                             suggestionsList.classList.add('hidden'); 
                                         }
                                    }
                                }

                                skillInput.addEventListener('input', function() {
                                    showSuggestions(this.value);
                                });

                                // Show full list on focus
                                skillInput.addEventListener('focus', function() {
                                    showSuggestions(this.value);
                                });

                                // Prevent list closing when clicking inside
                                // Already handled by the document click listener logic slightly, but let's Ensure
                                
                                // Close suggestions on click outside
                                document.addEventListener('click', function(e) {
                                    // If click is NOT on input AND NOT on list
                                    if (!skillInput.contains(e.target) && !suggestionsList.contains(e.target)) {
                                        suggestionsList.classList.add('hidden');
                                    }
                                });
                            });
                        </script>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Years of Experience</label>
                            <input type="text" name="experience" value="{{ old('experience', $user->experience) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent">
                            @error('experience') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Banking Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        Banking Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bank Name</label>
                            <select name="bankName" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent">
                                <option value="">Select Bank</option>
                                @foreach(['Maybank', 'CIMB Bank', 'Public Bank', 'RHB Bank', 'Hong Leong Bank', 'AmBank', 'UOB Malaysia', 'Bank Rakyat', 'OCBC Bank', 'HSBC Bank', 'Bank Islam', 'Affin Bank', 'Alliance Bank', 'Standard Chartered', 'Citibank', 'Bank Simpanan Nasional (BSN)', 'Bank Muamalat', 'Agrobank'] as $bank)
                                    <option value="{{ $bank }}" {{ old('bankName', $user->bankName) == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                @endforeach
                            </select>
                            @error('bankName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bank Account Number</label>
                            <input type="text" name="bankAccountNumber" value="{{ old('bankAccountNumber', $user->bankAccountNumber) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a8a5f] focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                     <a href="{{ route('facilitator.profile') }}" class="px-6 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#1a8a5f] hover:bg-[#15704d] text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
