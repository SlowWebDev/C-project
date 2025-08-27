<!-- Available Positions Section -->
<section class="py-16 md:py-28 bg-cover bg-center bg-no-repeat min-h-screen"
    style="background-image: url('{{ asset('assets/images/home/why-choose-us/why-choose-us-background.png') }}')">
        
        <div class="max-w-6xl mx-auto text-white">

            <!-- Position Switcher -->
            <div class="flex justify-start mb-10 gap-4">
                @foreach($jobs as $index => $job)
                <button 
                    onclick="showJob({{ $index }})"
                    class="job-btn px-6 py-2.5 rounded-lg font-medium text-sm bg-white/10 hover:bg-white/20 transition-all"
                    id="job-btn-{{ $index }}" data-job-id="{{ $job->id }}">
                    {{ $job->title }}
                </button>
                @endforeach
            </div>

            <!-- Position Content -->
            @foreach($jobs as $index => $job)
            <div class="position-content mb-16 {{ $index == 0 ? '' : 'hidden' }}" id="job-content-{{ $index }}">

                <!-- Job Title -->
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-6 drop-shadow-lg text-orange-600">
                    {{ $job->title }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">
                    
                    <!-- Job Requirements -->
                    <div>
                        <div class="space-y-3 mb-8">
                            @foreach($job->requirements as $requirement)
                            <div class="flex items-start">
                                <span class="text-orange-400 mr-2 font-bold">-</span>
                                <p class="drop-shadow text-sm sm:text-base">{{ $requirement }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Job Application Form -->
                    <div>
                        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl">
                            <h3 class="text-2xl font-bold mb-6 text-gray-900">Apply for this Position</h3>

                            <form id="job-form-{{ $job->id }}" action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="job_id" value="{{ $job->id }}">

                                <!-- First & Last Name -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">First Name</label>
                                        <input type="text" name="first_name" required placeholder="First Name"
                                            class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last Name</label>
                                        <input type="text" name="last_name" required placeholder="Last Name"
                                            class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                                    <input type="email" name="email" required placeholder="Email"
                                        class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                                    <input type="tel" name="phone" placeholder="Phone Number" id="phone-{{ $job->id }}"
                                        class="phone-input w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                </div>

                                <!-- CV Upload -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload CV (PDF only)</label>
                                    <div class="relative">
                                        <input type="file" name="cv" id="cv-{{ $job->id }}" accept=".pdf" required
                                               class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm focus:ring-1 focus:ring-orange-500 focus:border-orange-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Max file size: 5MB
                                            </span>
                                        </div>
                                    </div>
                                </div>



                                <!-- Submit -->
                                <button type="submit"
                                    class="w-full h-12 bg-orange-500 text-white rounded-lg font-medium text-base hover:bg-orange-600 transition-colors">
                                    Submit Application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
function showJob(jobIndex) {
    // Hide all content and reset buttons
    document.querySelectorAll('.position-content').forEach(content => content.classList.add('hidden'));
    document.querySelectorAll('.job-btn').forEach(btn => {
        btn.classList.remove('bg-white/30');
        btn.classList.add('bg-white/10');
    });
    
    // Show selected content and highlight button
    const content = document.getElementById('job-content-' + jobIndex);
    const button = document.getElementById('job-btn-' + jobIndex);
    
    if (content) content.classList.remove('hidden');
    if (button) {
        button.classList.remove('bg-white/10');
        button.classList.add('bg-white/30');
    }
}

// Initialize first job on load
document.addEventListener('DOMContentLoaded', () => showJob(0));
</script>

