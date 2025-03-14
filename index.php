<?php
// Initialize session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A-Panel | File Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bfd;
            --secondary: #3f51b5;
            --light: #f5f5f5;
            --dark: #333;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f8f9fe;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            padding: 20px 0;
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 100;
            top: 0;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 8px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        .nav-menu a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-menu a:hover {
            color: var(--primary);
        }
        
        .action-btn {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(74, 107, 253, 0.2);
        }
        
        .hero {
            padding: 150px 0 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .hero-text h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 1s forwards 0.3s;
        }
        
        .hero-text p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #666;
            line-height: 1.6;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 1s forwards 0.5s;
        }
        
        .hero-btns {
            display: flex;
            gap: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 1s forwards 0.7s;
        }
        
        .hero-img {
            opacity: 0;
            transform: translateX(20px);
            animation: fadeIn 1s forwards 0.9s;
        }
        
        .hero-img img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 20px 30px rgba(0,0,0,0.1);
        }
        
        .features {
            padding: 80px 0;
            background: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .feature-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .pricing {
            padding: 80px 0;
            background: #f8f9fe;
        }
        
        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .pricing-card {
            background: white;
            border-radius: 10px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .pricing-card.popular {
            transform: scale(1.05);
        }
        
        .pricing-card.popular::before {
            content: "POPULAR";
            position: absolute;
            top: 15px;
            right: -30px;
            background: var(--primary);
            color: white;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .pricing-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .pricing-card .price {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary);
        }
        
        .pricing-card .price span {
            font-size: 1rem;
            font-weight: 400;
            color: #666;
        }
        
        .storage-amount {
            font-size: 1.2rem;
            margin-bottom: 30px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .pricing-features {
            list-style: none;
            margin-bottom: 30px;
        }
        
        .pricing-features li {
            margin-bottom: 10px;
            color: #666;
        }
        
        .pricing-features li i {
            color: var(--success);
            margin-right: 5px;
        }
        
        .pricing-btn {
            display: inline-block;
            padding: 10px 30px;
            background: var(--primary);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .pricing-btn:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(74, 107, 253, 0.2);
        }
        
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        .footer-column p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #bbb;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            text-align: center;
            color: #bbb;
        }
        
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="#" class="logo"><i class="fas fa-file-archive"></i> A-Pannel</a>
            <ul class="nav-menu">
                <li><a href="#features">Features</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>

            <?php 


            if(isset($_SESSION["user_id"])) {
                echo '<a href="dashboard.php" class="action-btn
                ">Dashboard</a>';
            } else {
                echo '<a href="login.php" class="action-btn
                ">Login</a>';
            }
            ?>

        </div>
    </header>

    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Manage Your Files with Ease</h1>
                <p>A-Panel is an open-source file management system that allows you to store, organize, and access your files from anywhere. Get started today with our free plan.</p>
                <div class="hero-btns">
                    <a href="signup.php" class="action-btn">Get Started Free</a>
                    <a href="#pricing" class="action-btn" style="background: transparent; color: var(--dark); box-shadow: none;">View Plans</a>
                </div>
            </div>
            <div class="hero-img">
                <img src="./assets/preview.png" alt="A-Panel Dashboard Preview">
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>A-Panel comes with all the features you need to manage your files efficiently.</p>
            </div>
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>Drag & Drop Upload</h3>
                    <p>Upload your files with a simple drag and drop interface. Fast and convenient.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3>Easy File Sharing</h3>
                    <p>Share your files with others via direct links or email invitations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3>Secure Storage</h3>
                    <p>Your files are encrypted and stored securely in our cloud infrastructure.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Access Anywhere</h3>
                    <p>Access your files from any device, anywhere, at any time.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-title">
                <h2>Choose Your Plan</h2>
                <p>Select a plan that fits your needs. All plans come with our core features.</p>
            </div>
            <div class="pricing-cards">
                <div class="pricing-card">
                    <h3>Basic</h3>
                    <div class="price">$0 <span>/month</span></div>
                    <div class="storage-amount">100 MB Storage</div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> File Upload/Download</li>
                        <li><i class="fas fa-check"></i> Basic File Sharing</li>
                        <li><i class="fas fa-check"></i> Web Access</li>
                        <li><i class="fas fa-times"></i> Priority Support</li>
                    </ul>
                    <a href="signup.php?plan=basic" class="pricing-btn">Get Started</a>
                </div>
                <div class="pricing-card">
                    <h3>Student</h3>
                    <div class="price">$4.99 <span>/month</span></div>
                    <div class="storage-amount">500 MB Storage</div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> All Basic Features</li>
                        <li><i class="fas fa-check"></i> Advanced Sharing</li>
                        <li><i class="fas fa-check"></i> Mobile Access</li>
                        <li><i class="fas fa-times"></i> Priority Support</li>
                    </ul>
                    <a href="signup.php?plan=student" class="pricing-btn">Get Started</a>
                </div>
                <div class="pricing-card popular">
                    <h3>Pro</h3>
                    <div class="price">$9.99 <span>/month</span></div>
                    <div class="storage-amount">1 GB Storage</div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> All Student Features</li>
                        <li><i class="fas fa-check"></i> File Version History</li>
                        <li><i class="fas fa-check"></i> Offline Access</li>
                        <li><i class="fas fa-check"></i> Priority Support</li>
                    </ul>
                    <a href="signup.php?plan=pro" class="pricing-btn">Get Started</a>
                </div>
                <div class="pricing-card">
                    <h3>Ultra</h3>
                    <div class="price">$19.99 <span>/month</span></div>
                    <div class="storage-amount">Unlimited Storage</div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> All Pro Features</li>
                        <li><i class="fas fa-check"></i> Advanced Analytics</li>
                        <li><i class="fas fa-check"></i> Team Collaboration</li>
                        <li><i class="fas fa-check"></i> 24/7 Premium Support</li>
                    </ul>
                    <a href="signup.php?plan=ultra" class="pricing-btn">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>About A-Panel</h3>
                    <p>A-Panel is an open-source file management system designed to help users store and manage their files efficiently.</p>
                    <p>© <?php echo date('Y'); ?> A-Panel. All rights reserved.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:support@apanel.com">support@apanel.com</a></li>
                        <li><a href="tel:+1234567890">+1 (234) 567-890</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>A-Panel is an open-source project.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate feature cards on scroll
            const featureCards = document.querySelectorAll('.feature-card');
            const pricingCards = document.querySelectorAll('.pricing-card');
            
            const animateElements = (elements) => {
                elements.forEach((element, index) => {
                    setTimeout(() => {
                        element.style.animation = `fadeUp 1s forwards`;
                    }, 200 * index);
                });
            }
            
            // Intersection Observer for feature cards
            const featuresObserver = new IntersectionObserver((entries) => {
                if(entries[0].isIntersecting) {
                    animateElements(featureCards);
                    featuresObserver.unobserve(entries[0].target);
                }
            }, { threshold: 0.1 });
            
            // Intersection Observer for pricing cards
            const pricingObserver = new IntersectionObserver((entries) => {
                if(entries[0].isIntersecting) {
                    animateElements(pricingCards);
                    pricingObserver.unobserve(entries[0].target);
                }
            }, { threshold: 0.1 });
            
            if(document.querySelector('.features')) {
                featuresObserver.observe(document.querySelector('.features'));
            }
            
            if(document.querySelector('.pricing')) {
                pricingObserver.observe(document.querySelector('.pricing'));
            }
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if(targetId === '#') return;
                    
                    const target = document.querySelector(targetId);
                    
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html></ul></div>