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
        
        /* PREMIUM COLORS */
        .text-brand-gold { color: #C59C52; }
        .bg-brand-gold { background-color: #C59C52; }
        .hover-bg-brand-gold:hover { background-color: #A67E36; }
        
        /* 
         * CUSTOM ANIMATIONS 
         */
         
        /* 1. Fade In Up (For page load staggered entrance) */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in-up { 
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
            opacity: 0; /* Starts hidden */
        }
        
        /* Stagger delays so elements appear one after another */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }

        /* 2. Floating Effect (For the Hero Image) */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }

        /* 3. Pulse Glow (For the primary Call-To-Action button) */
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(197, 156, 82, 0.5); }
            70% { box-shadow: 0 0 0 20px rgba(197, 156, 82, 0); }
            100% { box-shadow: 0 0 0 0 rgba(197, 156, 82, 0); }
        }
        .animate-pulse-glow { animation: pulseGlow 2.5s infinite; }
        
        /* Custom Gold Glow shadow for the image */
        .shadow-gold-heavy { box-shadow: 0 20px 50px -12px rgba(197, 156, 82, 0.25); }
    </style>
</head>

<!-- Applied a deep, premium blue gradient background -->
<body class="bg-gradient-to-br from-[#071324] via-[#16437A] to-[#071324] text-white antialiased min-h-screen flex flex-col overflow-x-hidden">

    <!-- Navbar -->
    <nav class="w-full py-6 px-6 lg:px-12 flex justify-between items-center relative z-50 animate-fade-in-up">
        <div class="text-2xl font-black tracking-tight drop-shadow-lg">
            Suranga<span class="text-brand-gold">media</span>
        </div>
        <div class="flex gap-3 sm:gap-4">
            <!-- Transparent button for dark mode -->
            <a href="/student/login" class="px-5 py-2.5 text-sm font-bold text-white border-2 border-white/30 rounded-full hover:bg-white hover:text-[#16437A] transition-all duration-300">
                Log In
            </a>
            <!-- Solid Gold button -->
            <a href="/student/register" class="px-5 py-2.5 text-sm font-bold bg-brand-gold text-white rounded-full shadow-lg hover-bg-brand-gold transition-all duration-300 transform hover:-translate-y-1">
                Register
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center pt-2 pb-12 lg:py-0 relative">
        
        <!-- Subtle background glow effect behind everything -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8 relative z-10">
            
            <!-- Left Side: Logo & CTAs -->
            <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left space-y-8">
                
                <!-- Brand Logo (Fade in 1st) -->
                <!-- Note: Removed mix-blend-mode so transparent PNGs look perfect on the dark background -->
                <div class="w-full max-w-md xl:max-w-lg animate-fade-in-up delay-100">
                    <img src="/images/brand-logo-new.png" alt="Suranga Gamage Logo" class="w-full h-auto drop-shadow-2xl">
                </div>

                <!-- Text (Fade in 2nd) -->
                <div class="space-y-4 px-4 lg:px-0 animate-fade-in-up delay-200">
                    <p class="text-lg sm:text-xl font-medium text-blue-100 leading-relaxed max-w-md">
                        Join Sri Lanka's leading digital platform for <span class="font-bold text-brand-gold drop-shadow-md">Mass Media</span> and Communication studies.
                    </p>
                </div>
                
                <!-- Call to Action Buttons (Fade in 3rd) -->
                <div class="flex flex-col w-full sm:w-auto sm:flex-row items-center gap-4 pt-4 animate-fade-in-up delay-300">
                    
                    <!-- Primary Button (Gold with Pulse Glow) -->
                    <a href="/student/register" class="w-full sm:w-auto px-8 py-4 bg-brand-gold text-white font-bold rounded-full animate-pulse-glow hover-bg-brand-gold transition-all duration-300 transform hover:-translate-y-1 text-center text-lg">
                        Register
                    </a>
                    
                    <!-- Secondary Button (Glassmorphism outline) -->
                    <a href="/student/login" class="w-full sm:w-auto px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-bold rounded-full hover:bg-white hover:text-[#16437A] transition-all duration-300 transform hover:-translate-y-1 text-center text-lg">
                        Login
                    </a>
                </div>
            </div>

            <!-- Right Side: Hero Profile Image (Fade in 4th AND Floats) -->
            <div class="w-full lg:w-1/2 flex justify-center lg:justify-end mt-8 lg:mt-0 animate-fade-in-up delay-400">
                
                <!-- The float animation wrapper -->
                <div class="animate-float w-full max-w-lg xl:max-w-xl">
                    <!-- Image container with glowing border -->
                    <div class="relative overflow-hidden rounded-2xl shadow-gold-heavy border border-white/20 bg-gradient-to-t from-[#0E2E56] to-transparent">
                        <!-- Make sure to use the exact path to your new photo -->
                        <img src="/images/suranga-hero-new.jpg" alt="Suranga Gamage" class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
                
            </div>
            
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-blue-200/60 text-sm font-medium relative z-50 animate-fade-in-up delay-400">
        <p>&copy; 2026 Surangamedia. Developed by NC Enterprises. All rights reserved.</p>
    </footer>

</body>
</html>