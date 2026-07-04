<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surangamedia | Master Mass Media</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        
        /* PREMIUM COLORS */
        .text-brand-gold { color: #C59C52; }
        .bg-brand-gold { background-color: #C59C52; }
        .hover-bg-brand-gold:hover { background-color: #A67E36; }
        
        /* ANIMATIONS (Everything Kept) */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }

        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }

        @keyframes pulseGlow { 0% { box-shadow: 0 0 0 0 rgba(197, 156, 82, 0.5); } 70% { box-shadow: 0 0 0 20px rgba(197, 156, 82, 0); } 100% { box-shadow: 0 0 0 0 rgba(197, 156, 82, 0); } }
        .animate-pulse-glow { animation: pulseGlow 2.5s infinite; }
        
        .shadow-gold-heavy { box-shadow: 0 20px 50px -12px rgba(197, 156, 82, 0.25); }
    </style>
</head>

<body class="bg-gradient-to-br from-[#071324] via-[#16437A] to-[#071324] text-white antialiased min-h-screen flex flex-col">

    <nav class="w-full py-6 px-6 lg:px-12 flex flex-wrap justify-center sm:justify-between items-center gap-4 relative z-50 animate-fade-in-up">
        <div class="text-2xl font-black tracking-tight drop-shadow-lg">
            Suranga<span class="text-brand-gold">media</span>
        </div>
        <div class="flex gap-3">
            <a href="/student/login" class="px-5 py-2.5 text-sm font-bold text-white border-2 border-white/30 rounded-full hover:bg-white hover:text-[#16437A] transition-all duration-300">
                Login
            </a>
            <a href="/student/register" class="px-5 py-2.5 text-sm font-bold bg-brand-gold text-white rounded-full shadow-lg hover-bg-brand-gold transition-all duration-300">
                Register
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-10 px-0 sm:px-6 relative pb-16 lg:pb-10">
        
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] lg:w-[800px] lg:h-[800px] bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-12 w-full relative z-10">
            
            <div class="w-full lg:w-1/2 flex flex-col items-center text-center lg:items-start lg:text-left space-y-8 px-6 lg:px-0">
                
                <div class="w-full max-w-[280px] lg:max-w-sm animate-fade-in-up delay-100">
                    <img src="/images/brand-logo-new.png" alt="Logo" class="w-full h-auto drop-shadow-2xl">
                </div>
                
                <div class="space-y-4 animate-fade-in-up delay-200">
                    <p class="text-base sm:text-xl font-medium text-blue-100 leading-relaxed max-w-md">
                        Join Sri Lanka's leading digital platform for <br class="block sm:hidden">
                        <span class="font-bold text-brand-gold">Mass Media</span> and Communication studies.
                    </p>
                </div>
                
                <div class="flex flex-col w-full max-w-[280px] sm:max-w-md sm:flex-row gap-4 animate-fade-in-up delay-300">
                    <a href="/student/register" class="w-full sm:w-1/2 px-6 py-4 bg-brand-gold text-white font-bold rounded-full animate-pulse-glow text-center text-lg hover-bg-brand-gold transition-all duration-300">
                        Register
                    </a>
                    <a href="/student/login" class="w-full sm:w-1/2 px-6 py-4 bg-white/10 border border-white/20 text-white font-bold rounded-full text-center text-lg hover:bg-white hover:text-[#16437A] transition-all duration-300">
                        Login
                    </a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 flex justify-center animate-fade-in-up delay-400 px-6 lg:px-0">
                <div class="animate-float w-full max-w-sm lg:max-w-lg">
                    <div class="relative rounded-2xl shadow-gold-heavy border border-white/20 overflow-hidden">
                        <img src="/images/suranga-hero-new.jpg" alt="Suranga Gamage" class="w-full h-auto object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <footer class="py-6 text-center text-blue-200/60 text-sm relative z-50 animate-fade-in-up delay-400">
        <p>&copy; 2026 Surangamedia. Developed by NC Enterprises. All rights reserved.</p>
    </footer>

</body>
</html>