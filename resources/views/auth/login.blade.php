<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS - Sign In</title>
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
            <h2 class="text-xl text-white font-semibold">Sign In</h2>
            <p class="text-slate-400 text-sm">Workforce Management System</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Login As -->
            <div>
                <label for="role" class="block text-sm font-medium text-slate-300 mb-1">Login As</label>
                <select id="role" name="role" class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="facilitator">Facilitator</option>
                    <option value="operation_manager">Operation Manager</option>
                </select>
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
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required
                        class="w-full bg-slate-700 border border-slate-600 rounded-md py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-200">
                        <!-- Simple Icon Placeholder -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 2.943 8.268 5.857 9.542 9.98.056.183.087.378.087.58 0 .202-.031.397-.087.58-.14.475-.316.936-.525 1.378M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="text-red-500 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-md transition duration-200">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300">Register here</a>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-700 text-xs text-slate-500">
            <p class="mb-2">Demo Credentials:</p>
            <p><span class="text-slate-400">Facilitator:</span> sarah.bonding@falcon.com</p>
            <p><span class="text-slate-400">Operation Manager:</span> admin@falcon.com</p>
            <p><span class="text-slate-400">Password:</span> password</p>
        </div>
    </div>

</body>
</html>
