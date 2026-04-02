<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSys - Cabinet Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a5f7a;
            --secondary-color: #f0b429;
            --dark-color: #0d3b4f;
            --light-color: #f5f7fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .navbar-brand span {
            color: var(--secondary-color);
        }
        
        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            padding: 100px 0;
            margin-top: 70px;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn-custom-primary {
            background: var(--secondary-color);
            color: var(--dark-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            margin: 5px;
            transition: all 0.3s;
        }
        
        .btn-custom-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-custom-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            margin: 5px;
            transition: all 0.3s;
        }
        
        .btn-custom-outline:hover {
            background: white;
            color: var(--primary-color);
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .feature-icon i {
            font-size: 35px;
            color: white;
        }
        
        .feature-card h4 {
            color: var(--dark-color);
            margin-bottom: 15px;
        }
        
        .stats-section {
            background: var(--light-color);
            padding: 60px 0;
        }
        
        .stat-box {
            text-align: center;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
        }
        
        .language-selector {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            border-radius: 50px;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        .language-selector img {
            width: 24px;
            margin: 0 5px;
            cursor: pointer;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>
    <!-- Language Selector -->
    <div class="language-selector">
        <img src="https://flagcdn.com/w20/fr.png" alt="Français" onclick="changeLanguage('fr')">
        <img src="https://flagcdn.com/w20/ar.png" alt="العربية" onclick="changeLanguage('ar')">
        <span class="ms-2" id="langText">Français</span>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                Health<span>Sys</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#accueil" data-lang-key="nav_home">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services" data-lang-key="nav_services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about" data-lang-key="nav_about">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact" data-lang-key="nav_contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-custom-outline ms-3" href="{{ route('login') }}" style="border-color: var(--primary-color); color: var(--primary-color);">
                            <i class="fas fa-sign-in-alt"></i> <span data-lang-key="login">Se connecter</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="accueil">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 animate">
                    <h1 data-lang-key="hero_title">Bienvenue sur <span style="color: var(--secondary-color);">HealthSys</span></h1>
                    <p data-lang-key="hero_desc">Système intelligent de gestion de cabinet médical. Simple, rapide et efficace pour la gestion des patients, rendez-vous et dossiers médicaux.</p>
                    <div>
                        <a href="{{ route('login') }}" class="btn btn-custom-primary">
                            <i class="fas fa-sign-in-alt"></i> <span data-lang-key="login_btn">Se connecter</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-custom-outline">
                            <i class="fas fa-user-plus"></i> <span data-lang-key="register_btn">S'inscrire (Patient)</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-center animate">
                    <img src="https://www.vudailleurs.com/wp-content/uploads/2016/11/dididi-e1478470996278.jpg" alt="Doctors" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" data-lang-key="services_title">Nos Services</h2>
                <p class="text-muted" data-lang-key="services_desc">Une solution complète pour la gestion de votre cabinet médical</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4 data-lang-key="service1_title">Gestion des rendez-vous</h4>
                        <p data-lang-key="service1_desc">Planification et suivi des consultations facilement</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 data-lang-key="service2_title">Dossiers patients</h4>
                        <p data-lang-key="service2_desc">Historique médical, ordonnances, examens</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h4 data-lang-key="service3_title">Facturation</h4>
                        <p data-lang-key="service3_desc">Gestion des paiements et factures</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 data-lang-key="service4_title">Statistiques</h4>
                        <p data-lang-key="service4_desc">Analyses et rapports détaillés</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">500+</div>
                        <p data-lang-key="stat_patients">Patients satisfaits</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">20+</div>
                        <p data-lang-key="stat_doctors">Médecins experts</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">1000+</div>
                        <p data-lang-key="stat_appointments">Rendez-vous traités</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">24/7</div>
                        <p data-lang-key="stat_support">Support disponible</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="https://img.freepik.com/free-vector/medical-appointment-illustration_23-2148850657.jpg" alt="About" class="img-fluid rounded-4">
                </div>
                <div class="col-md-6">
                    <h2 class="display-5 fw-bold mb-4" data-lang-key="about_title">À propos de HealthSys</h2>
                    <p data-lang-key="about_desc1">HealthSys est une plateforme moderne de gestion de cabinet médical conçue pour optimiser le travail des professionnels de santé.</p>
                    <p data-lang-key="about_desc2">Notre solution permet de centraliser toutes les opérations : gestion des patients, rendez-vous, facturation, dossiers médicaux et rapports statistiques.</p>
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                            <span data-lang-key="about_feature1">Interface intuitive et facile à utiliser</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                            <span data-lang-key="about_feature2">Sécurité des données médicales</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                            <span data-lang-key="about_feature3">Accès multi-plateformes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="bg-light py-5" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" data-lang-key="contact_title">Contactez-nous</h2>
                <p class="text-muted" data-lang-key="contact_desc">Une question ? Besoin d'assistance ? Notre équipe est à votre disposition.</p>
            </div>
            <div class="row">
                <div class="col-md-4 text-center">
                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                    <h5 data-lang-key="contact_address_title">Adresse</h5>
                    <p data-lang-key="contact_address">sidi bouzid</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-phone-alt fa-3x text-primary mb-3"></i>
                    <h5 data-lang-key="contact_phone_title">Téléphone</h5>
                    <p>+216 27 348 607</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 data-lang-key="contact_email_title">Email</h5>
                    <p>Anissa@healthsys.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4>Health<span style="color: var(--secondary-color);">Sys</span></h4>
                    <p data-lang-key="footer_desc">Système innovant pour la gestion des cabinets médicaux.</p>
                </div>
                <div class="col-md-4">
                    <h5 data-lang-key="footer_links">Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="#accueil" class="text-white text-decoration-none">Accueil</a></li>
                        <li><a href="#services" class="text-white text-decoration-none">Services</a></li>
                        <li><a href="#about" class="text-white text-decoration-none">À propos</a></li>
                        <li><a href="#contact" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 data-lang-key="footer_support">Support</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone-alt me-2"></i> +216 27 348 607</li>
                        <li><i class="fas fa-envelope me-2"></i> Anissa@healthsys.com</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2026 HealthSys. <span data-lang-key="footer_copyright">Tous droits réservés.</span></p>
            </div>
        </div>
    </footer>

    <script>
        const translations = {
            fr: {
                nav_home: "Accueil",
                nav_services: "Services",
                nav_about: "À propos",
                nav_contact: "Contact",
                login: "Se connecter",
                hero_title: "Bienvenue sur HealthSys",
                hero_desc: "Système intelligent de gestion de cabinet médical. Simple, rapide et efficace pour la gestion des patients, rendez-vous et dossiers médicaux.",
                login_btn: "Se connecter",
                register_btn: "S'inscrire (Patient)",
                services_title: "Nos Services",
                services_desc: "Une solution complète pour la gestion de votre cabinet médical",
                service1_title: "Gestion des rendez-vous",
                service1_desc: "Planification et suivi des consultations facilement",
                service2_title: "Dossiers patients",
                service2_desc: "Historique médical, ordonnances, examens",
                service3_title: "Facturation",
                service3_desc: "Gestion des paiements et factures",
                service4_title: "Statistiques",
                service4_desc: "Analyses et rapports détaillés",
                stat_patients: "Patients satisfaits",
                stat_doctors: "Médecins experts",
                stat_appointments: "Rendez-vous traités",
                stat_support: "Support disponible",
                about_title: "À propos de HealthSys",
                about_desc1: "HealthSys est une plateforme moderne de gestion de cabinet médical conçue pour optimiser le travail des professionnels de santé.",
                about_desc2: "Notre solution permet de centraliser toutes les opérations : gestion des patients, rendez-vous, facturation, dossiers médicaux et rapports statistiques.",
                about_feature1: "Interface intuitive et facile à utiliser",
                about_feature2: "Sécurité des données médicales",
                about_feature3: "Accès multi-plateformes",
                contact_title: "Contactez-nous",
                contact_desc: "Une question ? Besoin d'assistance ? Notre équipe est à votre disposition.",
                contact_address_title: "Adresse",
                contact_address: "sidi bouzid",
                contact_phone_title: "Téléphone",
                contact_email_title: "Email",
                footer_desc: "Système innovant pour la gestion des cabinets médicaux.",
                footer_links: "Liens rapides",
                footer_support: "Support",
                footer_copyright: "Tous droits réservés."
            },
            ar: {
                nav_home: "الرئيسية",
                nav_services: "الخدمات",
                nav_about: "من نحن",
                nav_contact: "اتصل بنا",
                login: "تسجيل الدخول",
                hero_title: "مرحباً بكم في HealthSys",
                hero_desc: "نظام ذكي لإدارة العيادات الطبية. بسيط وسريع وفعال لإدارة المرضى والمواعيد والملفات الطبية.",
                login_btn: "تسجيل الدخول",
                register_btn: "التسجيل (مريض)",
                services_title: "خدماتنا",
                services_desc: "حل متكامل لإدارة عيادتك الطبية",
                service1_title: "إدارة المواعيد",
                service1_desc: "تخطيط ومتابعة الاستشارات بسهولة",
                service2_title: "ملفات المرضى",
                service2_desc: "التاريخ الطبي، الوصفات الطبية، الفحوصات",
                service3_title: "الفواتير",
                service3_desc: "إدارة المدفوعات والفواتير",
                service4_title: "الإحصائيات",
                service4_desc: "تحليلات وتقارير مفصلة",
                stat_patients: "مرضى راضون",
                stat_doctors: "أطباء خبراء",
                stat_appointments: "مواعيد تمت معالجتها",
                stat_support: "دعم متوفر",
                about_title: "عن HealthSys",
                about_desc1: "HealthSys هو منصة حديثة لإدارة العيادات الطبية مصممة لتحسين عمل المتخصصين في الرعاية الصحية.",
                about_desc2: "يتيح حلنا مركزية جميع العمليات: إدارة المرضى والمواعيد والفواتير والملفات الطبية والتقارير الإحصائية.",
                about_feature1: "واجهة بديهية وسهلة الاستخدام",
                about_feature2: "أمن البيانات الطبية",
                about_feature3: "وصول متعدد المنصات",
                contact_title: "اتصل بنا",
                contact_desc: "لديك سؤال؟ بحاجة إلى مساعدة؟ فريقنا في خدمتك.",
                contact_address_title: "العنوان",
                contact_address: "sidibouzid",
                contact_phone_title: "الهاتف",
                contact_email_title: "البريد الإلكتروني",
                footer_desc: "نظام مبتكر لإدارة العيادات الطبية.",
                footer_links: "روابط سريعة",
                footer_support: "الدعم",
                footer_copyright: "جميع الحقوق محفوظة."
            }
        };

        let currentLang = 'fr';

        function changeLanguage(lang) {
            currentLang = lang;
            document.getElementById('langText').innerText = lang === 'fr' ? 'Français' : 'العربية';
            
            document.querySelectorAll('[data-lang-key]').forEach(el => {
                const key = el.getAttribute('data-lang-key');
                if (translations[lang][key]) {
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.placeholder = translations[lang][key];
                    } else {
                        el.innerHTML = translations[lang][key];
                    }
                }
            });
            
            if (lang === 'ar') {
                document.body.style.direction = 'rtl';
                document.body.style.textAlign = 'right';
            } else {
                document.body.style.direction = 'ltr';
                document.body.style.textAlign = 'left';
            }
            
            localStorage.setItem('healthsys_lang', lang);
        }
        
        const savedLang = localStorage.getItem('healthsys_lang');
        if (savedLang) {
            changeLanguage(savedLang);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>