<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nexora Parent Hub') }} - Privacy Policy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-950">

    <!-- ============================================
    NAVIGATION
    ============================================ -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-red-600 to-orange-500 rounded-lg flex items-center justify-center text-white font-extrabold text-sm shadow-lg shadow-red-500/30">
                        N
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-gray-900 dark:text-white">Nexora</span>
                        <span class="text-sm font-medium text-red-600 dark:text-red-500">Parent Hub</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="/#features" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">Features</a>
                    <a href="/#how-it-works" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">How It Works</a>
                    <a href="/#institutes" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">Institutes</a>
                    <a href="/#api" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">API</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 px-4 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                            Dashboard
                        </a>
                    @else
                        <a href="#" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 px-4 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="#" class="text-sm font-medium bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow-md shadow-red-500/30 hover:shadow-lg hover:shadow-red-500/40 transition-all hover:-translate-y-0.5">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================
    PRIVACY POLICY CONTENT
    ============================================ -->
    <section class="pt-28 pb-16 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Privacy Policy
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white mb-3">
                    Nexora Parent Hub
                    <span class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent block">
                        Privacy Policy
                    </span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Last Updated: {{ date('F Y') }}
                </p>
            </div>

            <!-- Privacy Content -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="p-8 md:p-12 space-y-8">

                    <!-- Section 1 -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-red-600 dark:text-red-400 font-bold text-sm">1</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Introduction</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            Welcome to <strong class="text-gray-900 dark:text-white">Nexora Parent Hub</strong>. 
                            This Privacy Policy explains how we collect, use, and protect your 
                            personal information when you use our platform.
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14 mt-2">
                            By using this platform, you agree to the practices described in 
                            this Privacy Policy.
                        </p>
                    </div>

                    <!-- Section 2 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm">2</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Information We Collect</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14 mb-3">
                            We may collect the following information:
                        </p>
                        <ul class="ml-14 space-y-2">
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Student ID
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Student Name
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Grade Information
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Profile Photograph
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Parent Contact Details
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Notification Token (Firebase Cloud Messaging)
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-red-500 dark:text-red-400 mt-1">•</span>
                                Device Information
                            </li>
                        </ul>
                    </div>

                    <!-- Section 3 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-sm">3</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">How We Use Your Information</h2>
                        </div>
                        <ul class="ml-14 space-y-2">
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Authenticate your account
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Display student information
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Show class schedules
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Display upcoming examinations
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Display payment information
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Send important notifications
                            </li>
                            <li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
                                <span class="text-green-500 dark:text-green-400 mt-1">✓</span>
                                Improve application performance
                            </li>
                        </ul>
                    </div>

                    <!-- Section 4 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold text-sm">4</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            Nexora Parent Hub uses 
                            <span class="inline-block bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 font-semibold px-3 py-1 rounded-lg">Firebase Cloud Messaging (FCM)</span>
                            to send important educational notifications, announcements, reminders, and updates.
                        </p>
                    </div>

                    <!-- Section 5 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-sm">5</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data Security</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            We implement appropriate technical and organizational measures 
                            to protect your personal information from unauthorized access, 
                            disclosure, alteration, or destruction.
                        </p>
                    </div>

                    <!-- Section 6 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold text-sm">6</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data Sharing</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            We do not sell or rent your personal information. 
                            Your information is shared only with your educational institute 
                            for providing educational services.
                        </p>
                    </div>

                    <!-- Section 7 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center text-teal-600 dark:text-teal-400 font-bold text-sm">7</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Children's Privacy</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            This platform is intended for parents registered with an educational institute. 
                            Student information is managed only by the respective institution.
                        </p>
                    </div>

                    <!-- Section 8 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">8</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Changes to this Privacy Policy</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            We may update this Privacy Policy from time to time. 
                            Any changes will be published on this page.
                        </p>
                    </div>

                    <!-- Section 9 -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold text-sm">9</div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Contact Us</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed ml-14">
                            If you have any questions regarding this Privacy Policy, 
                            please contact your educational institute or the system administrator.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-8 text-center">
                <a href="/" class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 font-semibold hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-gradient-to-br from-red-600 to-orange-500 rounded-lg flex items-center justify-center text-white font-extrabold text-sm">
                            N
                        </div>
                        <span class="text-lg font-extrabold">Nexora</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Centralized parent management system for multiple educational institutes.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Roadmap</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ url('/privacy-policy') }}" class="hover:text-white transition-colors text-red-400">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-400">
                    © {{ date('Y') }} Nexora Parent Hub. All rights reserved. v{{ app()->version() }}
                </p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 01-1.93.07 4.28 4.28 0 004 2.98 8.521 8.521 0 01-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 100-8 4 4 0 000 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>