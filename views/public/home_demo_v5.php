<?php
/**
 * الصفحة الرئيسية - النموذج الخامس (The Digital Machine / Bespoke Identity)
 * نظام المُوَفِّي - هوية الماكينة الرقمية
 * Concept: The website operates like a high-end Ricoh machine.
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المُوَفِّي | الهندسة الرقمية</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@200;400;600;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --machine-grey: #1a1a1a;
            --panel-black: #0d0d0d;
            --ricoh-red: #cf142b; /* Ricoh Brand Color */
            --paper-white: #f0f0f0;
            --tech-blue: #00add8;
            --ink-c: #00aeef;
            --ink-m: #ec008c;
            --ink-y: #fff200;
            --ink-k: #231f20;
            --grid-line: rgba(255,255,255,0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--panel-black);
            color: var(--paper-white);
            font-family: 'Changa', sans-serif;
            margin: 0;
            overflow-x: hidden;
            background-image: 
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* --- Tech Typos --- */
        .mono { font-family: 'Share Tech Mono', monospace; }
        
        h1, h2, h3 { text-transform: uppercase; margin: 0; line-height: 1; }

        /* --- UI: The Control Panel (Sidebar) --- */
        .control-panel {
            position: fixed;
            right: 0; top: 0;
            width: 80px; height: 100vh;
            background: var(--machine-grey);
            border-left: 1px solid #333;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 0;
            box-shadow: -5px 0 20px rgba(0,0,0,0.5);
        }

        .panel-logo {
            width: 50px; height: 50px;
            background: var(--ricoh-red);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            border-radius: 4px;
            margin-bottom: auto;
            font-size: 1.5rem;
        }

        .panel-btn {
            width: 50px; height: 50px;
            margin: 10px 0;
            border: 1px solid #444;
            background: #222;
            color: #666;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            position: relative;
        }
        
        .panel-btn:hover, .panel-btn.active {
            background: #333;
            color: var(--tech-blue);
            border-color: var(--tech-blue);
            box-shadow: 0 0 10px rgba(0, 173, 216, 0.3);
        }
        
        .panel-btn::after {
            content: attr(data-label);
            position: absolute;
            right: 70px;
            background: var(--tech-blue);
            color: #000;
            padding: 2px 8px;
            font-size: 0.8rem;
            opacity: 0;
            pointer-events: none;
            transition: 0.2s;
            white-space: nowrap;
            font-family: 'Share Tech Mono', monospace;
        }
        
        .panel-btn:hover::after { opacity: 1; right: 60px; }

        .status-indicators {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .led {
            width: 8px; height: 8px;
            background: #222;
            border-radius: 50%;
            border: 1px solid #444;
        }
        .led.on { background: #0f0; box-shadow: 0 0 5px #0f0; }
        .led.busy { background: var(--ricoh-red); box-shadow: 0 0 5px var(--ricoh-red); animation: blink 0.5s infinite; }

        @keyframes blink { 50% { opacity: 0.3; } }

        /* --- Main Layout --- */
        .stage {
            width: calc(100% - 80px);
            margin-right: 80px; /* Space for sidebar */
            position: relative;
        }

        /* --- Hero: The Scanner --- */
        .scanner-hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 10%;
            overflow: hidden;
        }

        .scanner-bar {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 50px;
            background: linear-gradient(to bottom, rgba(0, 173, 216, 0), rgba(0, 173, 216, 0.5), rgba(0, 173, 216, 0));
            z-index: 1;
            box-shadow: 0 0 20px var(--tech-blue);
            pointer-events: none;
            opacity: 0;
        }

        .hero-data {
            position: relative;
            z-index: 2;
        }

        .sys-id {
            color: var(--tech-blue);
            border: 1px solid var(--tech-blue);
            padding: 5px 10px;
            display: inline-block;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .main-headline {
            font-size: 6rem;
            font-weight: 800;
            color: transparent;
            -webkit-text-stroke: 2px #fff;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .main-headline::before {
            content: attr(data-text);
            position: absolute;
            top: 0; left: 0;
            width: 0; height: 100%;
            color: #fff;
            overflow: hidden;
            border-right: 4px solid var(--ricoh-red);
            transition: width 2s;
            -webkit-text-stroke: 0;
        }
        
        .scanned .main-headline::before { width: 100%; }

        .sub-headline {
            font-size: 2rem;
            color: #888;
            max-width: 600px;
        }

        /* --- Section: Paper Feed (Services) --- */
        .paper-feed-section {
            padding: 5rem 10%;
            background: #111;
            position: relative;
            z-index: 5;
        }

        .sheets-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            perspective: 1000px;
        }

        .sheet {
            background: var(--paper-white);
            color: #000;
            height: 400px;
            padding: 2rem;
            position: relative;
            transform-origin: top center;
            transform: rotateX(-90deg); /* Start hidden */
            opacity: 0;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.8s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .sheet::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 5px;
            background: repeating-linear-gradient(90deg, #ccc 0, #ccc 10px, transparent 10px, transparent 20px);
        }

        .sheet-icon {
            font-size: 3rem;
            color: var(--ricoh-red);
            margin-bottom: 2rem;
        }

        .sheet-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cmyk-dots {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 5px;
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .c { background: var(--ink-c); }
        .m { background: var(--ink-m); }
        .y { background: var(--ink-y); }
        .k { background: var(--ink-k); }

        /* --- Section: Mechanics (About/Stats) --- */
        .mechanics-section {
            padding: 10rem 10%;
            position: relative;
            overflow: hidden;
        }

        .gear {
            position: absolute;
            color: #222;
            z-index: -1;
            animation: spin 20s linear infinite;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }

        .stat-box {
            border: 1px solid #444;
            background: rgba(0,0,0,0.8);
            padding: 2rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }
        
        .stat-box:hover {
            border-color: var(--tech-blue);
            transform: translateX(-10px);
        }

        .stat-val {
            font-family: 'Share Tech Mono';
            font-size: 3rem;
            color: var(--tech-blue);
        }

        /* --- Footer: Shutdown Sequence --- */
        .terminal-footer {
            background: #000;
            padding: 4rem 10%;
            font-family: 'Share Tech Mono', monospace;
            border-top: 2px solid var(--ricoh-red);
        }

        .blinking-cursor {
            display: inline-block;
            width: 10px; height: 20px;
            background: var(--ricoh-red);
            animation: blink 1s infinite;
        }

        .btn-shutdown {
            background: var(--ricoh-red);
            color: #fff;
            border: none;
            padding: 1rem 3rem;
            font-family: 'Changa', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 2rem;
        }
        
        .btn-shutdown:hover {
            background: #a00;
        }

    </style>
</head>
<body>

    <!-- SIDEBAR: The Machine Interface -->
    <aside class="control-panel">
        <div class="panel-logo">M</div>
        
        <div style="margin-top: 3rem;">
            <div class="panel-btn" data-label="الرئيسية" onclick="scrollToSec('hero')"><i class="fas fa-power-off"></i></div>
            <div class="panel-btn" data-label="الخدمات" onclick="scrollToSec('feed')"><i class="fas fa-layer-group"></i></div>
            <div class="panel-btn" data-label="الهندسة" onclick="scrollToSec('mech')"><i class="fas fa-cogs"></i></div>
            <div class="panel-btn" data-label="اتصل بنا" onclick="scrollToSec('end')"><i class="fas fa-phone-alt"></i></div>
        </div>

        <div class="status-indicators">
            <div class="led on" title="System Online"></div>
            <div class="led" title="Processing"></div>
            <div class="led" title="Error"></div>
        </div>
        
        <div class="mono" style="margin-top: 10px; font-size: 0.6rem; color: #444; writing-mode: vertical-rl;">WARMING UP</div>
    </aside>

    <main class="stage">
        
        <!-- HERO: Scanning Process -->
        <section class="scanner-hero" id="hero">
            <div class="scanner-bar"></div>
            
            <div class="hero-data">
                <span class="sys-id mono">SYS.ID: AL-MUWAFI-2024 // RICOH.PARTNER</span>
                <h1 class="main-headline" data-text="أداء الماكينة المثالي">أداء الماكينة المثالي</h1>
                <p class="sub-headline">
                    نحن لا ندير مكتبك، نحن نقوم بـ <span style="color: var(--ricoh-red); font-weight: 700;">هندسته.</span>
                    <br>
                    خدمة صيانة دقيقة كالليزر. قطع غيار أصلية كالحديد.
                </p>
                <div style="margin-top: 3rem;">
                    <a href="<?= BASE_URL ?>/maintenance" class="panel-btn" style="width: auto; padding: 0 2rem; font-weight: 700;">تشغيل النظام</a>
                </div>
            </div>
        </section>

        <!-- SERVICES: Paper Feed Animation -->
        <section class="paper-feed-section" id="feed">
            <div style="margin-bottom: 3rem; border-bottom: 1px dashed #444; padding-bottom: 1rem; display: flex; justify-content: space-between;">
                <h2 class="mono">TRAY 1: SERVICES</h2>
                <span class="mono" style="color: var(--tech-blue);">LOADING...</span>
            </div>

            <div class="sheets-container">
                <!-- Sheet 1 -->
                <div class="sheet">
                    <i class="fas fa-wrench sheet-icon"></i>
                    <h3 class="sheet-title">صيانة ميكانيكية</h3>
                    <p style="color: #444;">إصلاح الأعطال الميكانيكية بدقة المصنع. استبدال التروس، الرولات، ووحدات السخان بقطع ريكو الأصلية.</p>
                    <div class="cmyk-dots"><div class="dot c"></div><div class="dot m"></div></div>
                </div>
                
                <!-- Sheet 2 -->
                <div class="sheet">
                    <i class="fas fa-microchip sheet-icon"></i>
                    <h3 class="sheet-title">برمجة الأنظمة</h3>
                    <p style="color: #444;">تحديث الفيرموير (Firmware)، تعريفات الطابعات، وحلول الأمان الرقمي لحماية مستنداتك.</p>
                    <div class="cmyk-dots"><div class="dot y"></div><div class="dot k"></div></div>
                </div>

                <!-- Sheet 3 -->
                <div class="sheet">
                    <i class="fas fa-box sheet-icon"></i>
                    <h3 class="sheet-title">إمداد لوجستي</h3>
                    <p style="color: #444;">توفير الأحبار والورق وقطع الغيار بشكل دوري ومجدول لضمان عدم توقف العمل دقيقة واحدة.</p>
                    <div class="cmyk-dots"><div class="dot c"></div><div class="dot k"></div></div>
                </div>
            </div>
        </section>

        <!-- MECHANICS: Stats & Info -->
        <section class="mechanics-section" id="mech">
            <i class="fas fa-cog gear" style="font-size: 40rem; top: -100px; left: -100px;"></i>
            <i class="fas fa-cog gear" style="font-size: 20rem; bottom: -50px; right: 50px; animation-direction: reverse; animation-duration: 15s;"></i>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
                <div>
                    <h2 style="font-size: 3rem; margin-bottom: 2rem;">المحرك الداخلي</h2>
                    <p style="color: #aaa; font-size: 1.2rem; line-height: 1.8;">
                        في "المُوَفِّي"، نعمل بدقة التروس المتشابكة. كل طلب صيانة يمر عبر دورة عمل محكمة لضمان السرعة والجودة.
                    </p>
                    <ul class="mono" style="margin-top: 2rem; color: var(--tech-blue); list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">> DIAGNOSTIC.............OK</li>
                        <li style="margin-bottom: 10px;">> PARTS_CHECK............OK</li>
                        <li style="margin-bottom: 10px;">> TECHNICIAN_DISPATCH....READY</li>
                    </ul>
                </div>
                
                <div>
                    <div class="stat-box">
                        <span>PRINT_COUNT</span>
                        <span class="stat-val counter">10M+</span>
                    </div>
                    <div class="stat-box">
                        <span>UPTIME</span>
                        <span class="stat-val counter">99.9%</span>
                    </div>
                    <div class="stat-box">
                        <span>CLIENTS</span>
                        <span class="stat-val counter">500</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER: Standard Output -->
        <footer class="terminal-footer" id="end">
            <p class="mono" style="color: #666;">> SYSTEM STATUS: WAITING FOR INPUT...</p>
            <h2 style="font-size: 3rem; margin: 1rem 0;">جاهز للبدء؟</h2>
            <p style="font-size: 1.2rem; color: #fff;">ابدأ طلب الصيانة الآن وسيقوم فريقنا بالاستجابة فوراً.</p>
            
            <a href="<?= BASE_URL ?>/maintenance" class="btn-shutdown">
                <i class="fas fa-power-off"></i> بدء التشغيل
            </a>
            
            <div style="margin-top: 4rem; display: flex; justify-content: space-between; color: #444; font-size: 0.9rem;">
                <span>AL-MUWAFI SYSTEMS © 2024</span>
                <span>RICOH AUTHORIZED</span>
            </div>
        </footer>

    </main>

    <script>
        function scrollToSec(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }

        // --- Animations ---
        
        // 1. Scanner Effect on Load
        const tl = gsap.timeline();
        
        tl.to('.scanner-bar', { opacity: 1, top: '100%', duration: 2, ease: 'linear', repeat: 1, yoyo: true })
          .to('.scanner-bar', { opacity: 0 })
          .add(() => { document.querySelector('#hero').classList.add('scanned'); });

        // 2. Paper Feed on Scroll
        gsap.registerPlugin(ScrollTrigger);
        
        const sheets = gsap.utils.toArray('.sheet');
        sheets.forEach((sheet, i) => {
            gsap.to(sheet, {
                rotateX: 0,
                opacity: 1,
                y: 0,
                duration: 0.8,
                delay: i * 0.2, // Stagger effect
                scrollTrigger: {
                    trigger: '.sheets-container',
                    start: 'top 70%'
                }
            });
        });

        // 3. LED Activity
        setInterval(() => {
            const leds = document.querySelectorAll('.led');
            const busyLed = leds[1]; // Business/Processing
            busyLed.classList.toggle('busy');
        }, 3000);

    </script>
</body>
</html>
