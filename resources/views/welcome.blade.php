<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nexora Parent Hub') }} - Centralized Parent Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white dark:bg-gray-950">
    
    <!-- ============================================
    NAVIGATION
    ============================================ -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-red-600 to-orange-500 rounded-lg flex items-center justify-center text-white font-extrabold text-sm shadow-lg shadow-red-500/30">
                        N
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-gray-900 dark:text-white">Nexora</span>
                        <span class="text-sm font-medium text-red-600 dark:text-red-500">Parent Hub</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#features" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">Features</a>
                    <a href="#how-it-works" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">How It Works</a>
                    <a href="#institutes" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">Institutes</a>
                    <a href="#api" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">API</a>
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
    HERO SECTION
    ============================================ -->
    <section class="pt-32 pb-16 px-4 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                        System Operational • v{{ app()->version() }}
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 text-gray-900 dark:text-white">
                        One Hub for All
                        <span class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent block">
                            Parent Needs
                        </span>
                    </h1>

                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed mb-8 max-w-lg">
                        Centralized platform connecting parents with their children's educational 
                        journey across multiple institutes. Track payments, attendance, results, 
                        and more from a single dashboard.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-8 py-4 rounded-xl shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 transition-all hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Get Started Free
                        </a>
                        <a href="#how-it-works" class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold px-8 py-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Watch Demo
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="mt-10 flex items-center gap-8">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-red-600 dark:text-red-400">10+</div>
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-400">1K+</div>
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-green-600 dark:text-green-400">50+</div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-bold text-gray-900 dark:text-white">Trusted</span> by institutes across Sri Lanka
                        </div>
                    </div>
                </div>

                <!-- Right - Dashboard Preview -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/30 dark:to-orange-950/30 rounded-3xl p-6 border border-gray-200 dark:border-gray-800">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6">
                            <!-- Dashboard Mockup -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-orange-500 rounded-full"></div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Parent Dashboard</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Welcome back, Kamal Perera</div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-lg"></div>
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-lg"></div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">2</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Children</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">LKR 0</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Due</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">95%</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Attendance</div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs">✓</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Payment Received</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Tuition fee - LKR 15,000</div>
                                    </div>
                                    <div class="text-xs text-green-600 dark:text-green-400">Today</div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs">📚</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Exam Results</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Mathematics - Grade A</div>
                                    </div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400">2 days ago</div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-100 dark:border-yellow-800/30">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xs">⏰</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Attendance Alert</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Late arrival today</div>
                                    </div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400">Today</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge -->
                    <div class="absolute -top-4 -right-4 bg-white dark:bg-gray-800 shadow-xl rounded-full px-4 py-2 border border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <span class="text-2xl">🚀</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Live Demo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    FEATURES SECTION
    ============================================ -->
    <section id="features" class="py-20 px-4 bg-gray-50 dark:bg-gray-900/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Features</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mt-2 mb-4">
                    Everything You Need as a Parent
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Access all your children's educational information from multiple institutes in one place.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">💰</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Payment Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Track tuition fees, payments history, and get instant payment reminders for all your children across institutes.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">📊</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Attendance Tracking</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monitor daily attendance, get notifications for absences, and view attendance patterns for each child.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">📝</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Exam Results</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View results, performance analytics, and track academic progress across all subjects and terms.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">👨‍🏫</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Teacher Details</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Access teacher profiles, contact information, and communication history with educators.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">📚</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Course Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View enrolled courses, schedules, and track course progress for each child in different institutes.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-2xl mb-4">🏫</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Institute Location</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Find institute locations, get directions, and access contact information for emergencies.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    HOW IT WORKS
    ============================================ -->
    <section id="how-it-works" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">How It Works</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mt-2 mb-4">
                    Simple & Secure Connection
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Get started in minutes with our seamless parent onboarding process.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Step 1 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg shadow-red-500/30">1</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Register Account</h3>
                    <p class="text-gray-600 dark:text-gray-400">Create your parent account with basic details and verify your identity.</p>
                </div>

                <!-- Arrow -->
                <div class="hidden md:flex items-center justify-center text-3xl text-gray-300 dark:text-gray-600">→</div>

                <!-- Step 2 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg shadow-blue-500/30">2</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Connect Children</h3>
                    <p class="text-gray-600 dark:text-gray-400">Add your children using their student ID and institute code.</p>
                </div>

                <!-- Arrow -->
                <div class="hidden md:flex items-center justify-center text-3xl text-gray-300 dark:text-gray-600">→</div>

                <!-- Step 3 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg shadow-green-500/30">3</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Start Tracking</h3>
                    <p class="text-gray-600 dark:text-gray-400">Access all information, get notifications, and stay connected with your children's education.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    INSTITUTES SECTION
    ============================================ -->
    <section id="institutes" class="py-20 px-4 bg-gray-50 dark:bg-gray-900/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Institutes</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mt-2 mb-4">
                    Connect Multiple Institutes
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    One account, unlimited institutes. All your children's data in one place.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 text-center">
                    <div class="text-4xl mb-3">🏫</div>
                    <h4 class="font-bold text-gray-900 dark:text-white">Institute A</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Colombo Branch</p>
                    <span class="inline-block mt-2 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full">Active</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 text-center">
                    <div class="text-4xl mb-3">🏫</div>
                    <h4 class="font-bold text-gray-900 dark:text-white">Institute B</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kandy Branch</p>
                    <span class="inline-block mt-2 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full">Active</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 text-center">
                    <div class="text-4xl mb-3">🏫</div>
                    <h4 class="font-bold text-gray-900 dark:text-white">Institute C</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Galle Branch</p>
                    <span class="inline-block mt-2 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-3 py-1 rounded-full">Maintenance</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 text-center flex flex-col items-center justify-center min-h-[140px]">
                    <div class="text-3xl mb-2">➕</div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Add Institute</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Coming soon</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    API ENDPOINTS
    ============================================ -->
    <section id="api" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">API</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mt-2 mb-4">
                    Developer Friendly API
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    RESTful API endpoints for seamless integration with existing systems.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-md bg-red-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800 dark:text-gray-200">/api/v1/login</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Parent authentication and institute discovery</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-md bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800 dark:text-gray-200">/api/v1/register-institute</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Register new institute in the parent hub</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-md bg-green-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800 dark:text-gray-200">/api/v1/register-student</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Register student for cross-institute access</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-md bg-yellow-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800 dark:text-gray-200">/api/v1/student/lookup</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Find student and their institute details</p>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="#" class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 font-semibold hover:underline">
                    View Full API Documentation
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================
    CTA SECTION
    ============================================ -->
    <section class="py-20 px-4 bg-gradient-to-r from-red-600 to-orange-500">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                Ready to Simplify Parent Management?
            </h2>
            <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                Join thousands of parents who are already using Nexora Parent Hub to stay connected with their children's education.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#" class="inline-flex items-center gap-2 bg-white text-red-600 font-bold px-8 py-4 rounded-xl hover:bg-gray-50 transition-all hover:-translate-y-0.5 shadow-lg shadow-black/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Create Free Account
                </a>
                <a href="#" class="inline-flex items-center gap-2 bg-transparent text-white font-semibold px-8 py-4 rounded-xl border-2 border-white hover:bg-white/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Schedule Demo
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
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
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
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
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