<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surangamedia | Master Mass Media</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* 
           Adjusted Hex Code: This is a deeper golden yellow to better match his jacket photo.
           If it still doesn't match perfectly, the rounded corners and shadows below will hide it! 
        */
        .bg-brand-yellow { background-color: #fcd116; }
        
        .text-brand-blue { color: #004aad; }
        .bg-brand-blue { background-color: #004aad; }
        .hover-bg-brand-blue:hover { background-color: #003682; }
        
        .bg-brand-red { background-color: #c8102e; }
        .hover-bg-brand-red:hover { background-color: #a50d25; }
    </style>
</head>
<body class="bg-brand-yellow text-slate-900 antialiased min-h-screen flex flex-col overflow-x-hidden">

    <!-- Navbar -->
    <nav class="w-full py-6 px-6 lg:px-12 flex justify-between items-center relative z-50">
        <div class="text-2xl font-black tracking-tight text-brand-blue drop-shadow-sm">
            Suranga<span class="text-slate-900">media</span>
        </div>
        <div class="flex gap-3 sm:gap-4">
            <!-- Redirected strictly to Student Login -->
            <a href="/student/login" class="px-5 py-2.5 text-sm font-bold text-brand-blue border-2 border-brand-blue rounded-full hover:bg-brand-blue hover:text-white transition-all duration-300">
                Log In
            </a>
            <!-- Redirected strictly to Student Register -->
            <a href="/student/register" class="px-5 py-2.5 text-sm font-bold bg-brand-red text-white rounded-full shadow-lg shadow-red-900/20 hover-bg-brand-red transition-all duration-300 transform hover:-translate-y-0.5">
                Register
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center pt-2 pb-12 lg:py-0">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8">
            
            <!-- Left Side: Logo & CTAs -->
            <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left space-y-8 z-10">
                
                <!-- Brand Logo Image with Blend Mode to hide the background box -->
                <div class="w-full max-w-md xl:max-w-lg transform hover:scale-105 transition-transform duration-500" style="mix-blend-mode: darken;">
                    <img src="/images/brand-logo.png" alt="Suranga Gamage Logo" class="w-full h-auto">
                </div>

                <div class="space-y-4 px-4 lg:px-0">
                    <p class="text-lg sm:text-xl font-bold text-slate-800 leading-relaxed max-w-md">
                        Join Sri Lanka's leading digital platform for Mass Media and Communication studies.
                    </p>
                </div>
                
                <!-- Call to Action Buttons (Student Only) -->
                <div class="flex flex-col w-full sm:w-auto sm:flex-row items-center gap-4 pt-4">
                    <a href="/student/register" class="w-full sm:w-auto px-8 py-4 bg-brand-red text-white font-bold rounded-full shadow-xl shadow-red-900/30 hover-bg-brand-red transition-all duration-300 transform hover:-translate-y-1 text-center text-lg">
                        Start Learning Today
                    </a>
                    <a href="/student/login" class="w-full sm:w-auto px-8 py-4 bg-brand-blue text-white font-bold rounded-full shadow-xl shadow-blue-900/30 hover-bg-brand-blue transition-all duration-300 transform hover:-translate-y-1 text-center text-lg">
                        Student Portal
                    </a>
                </div>
            </div>

            <!-- Right Side: Hero Profile Image -->
            <div class="w-full lg:w-1/2 flex justify-center lg:justify-end relative z-10 mt-4 lg:mt-0">
                <!-- Added rounded corners and a massive shadow to make it look like a premium card -->
                <div class="relative w-full max-w-lg xl:max-w-xl transform hover:-translate-y-2 transition-transform duration-500 rounded-3xl overflow-hidden shadow-2xl border-4 border-white/20">
                    <img src="/images/suranga-hero.jpg" alt="Suranga Gamage" class="w-full h-auto object-cover">
                </div>
            </div>
            
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-slate-800/80 text-sm font-semibold relative z-50">
        <p>&copy; 2026 Surangamedia. Developed by NC Enterprises. All rights reserved.</p>
    </footer>

</body>
</html>