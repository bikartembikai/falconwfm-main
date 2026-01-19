<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-slate-800 rounded-lg shadow-xl p-8 border border-slate-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-500 mb-2">WMS</h1>
            <h2 class="text-xl text-white font-semibold">Create Account</h2>
            <p class="text-slate-400 text-sm">Register as a Facilitator</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-1">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required
                    class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required
                    class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required
                    class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required
                    class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            @if ($errors->any())
                <div class="text-red-500 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-md transition duration-200">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300">Sign in here</a>
        </div>
    </div>

</body>
</html>
