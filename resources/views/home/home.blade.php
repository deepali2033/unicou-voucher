<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unicou Menu</title>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
<!-- PAGE LOADER -->
<div id="pageLoader">
  <div class="loader-content">
    <div class="loader-logo">
        <img src=" {{asset('images/company_logo.png')}} " alt="unicou logo">
    </div>
    <p class="loader-text">
      Global Learning • Digital Education • Career Growth
    </p>

    <div class="loader-dots">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
</div>


<div class="floatingNav scroll">
    <div class="listItem Sec1 active" data-target="Sec1"><span>Home</span></div>
    <div class="listItem Sec2" data-target="Sec2"><span>GLOBAL<br>AUDIENCE InUni</span></div>
    <div class="listItem Sec3" data-target="Sec3"><span>STUDY AT <br> UNICOU</span></div>
    <div class="listItem Sec4" data-target="Sec4"><span> INTERNATIONAL <br> AWARDING</span></div>
    <div class="listItem Sec5" data-target="Sec5"><span>LEARNING <br> SUPPORT</span></div>
    <div class="listItem Sec6" data-target="Sec6"><span>TECHNOLOGY <br> DRIVEN</span></div>
    <div class="listItem Sec7" data-target="Sec7"><span>UNICOU <br> LEARNING</span></div>
    <div class="listItem Sec8" data-target="Sec8"><span>Global <br> Education</span></div>
    <div class="listItem Sec9" data-target="Sec9"><span>Start <br> Learning</span></div>
</div>

<header class="site-header">
  <div class="logo">
    <img src="{{asset('images/company_logo.png')}}" alt="unicou logo">
  </div>

  <!-- Hamburger -->
  <div class="hamburger" id="openMenu">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<!-- Overlay -->
<div class="menu-overlay" id="menuOverlay"></div>

<!-- Slide Menu -->
<div class="side-menu" id="sideMenu">
  <div class="menu-header">
    <span class="close-btn" id="closeMenu">&times;</span>
  </div>

  <ul class="menu-links">
    <li><a href="#">ABOUT</a></li>
    <li><a href="#">STUDY ABROAD</a></li>
    <li><a href="#">EXAMS</a></li>
    <li><a href="#">LEARNING HUB</a></li>
    <li><a href="#">BLOGS</a></li>
    <li><a href="#">CONNECT</a></li>
  </ul>
</div>


<section class="hero-section padding_top_bot Sec1" id="Sec1" style="background-image: url('{{ asset('images/bgLeftTop.png') }}');">
    <div id="particles-bg"></div>

    <div class="container">
        <div class="hero-container">

            <!-- LEFT CONTENT -->
            <div class="hero-content">

            <span class="hero-top-text">
                Digital Learning, Global Qualifications & Career-Focused Education
            </span>

            <h1>
                UNICOU LEARNING <span>HUB</span>
            </h1>

            <div class="hero-text-block">
                <p>
                Welcome to <strong>UniCou Learning Hub</strong>, the dedicated learning
                management and education technology platform of <strong>UniCou Ltd.</strong>
                Designed for students, professionals, institutions, and global learners,
                our Learning Hub delivers <strong>flexible, technology-enabled education
                pathways</strong> that support academic growth, professional development,
                and international progression.
                </p>
            </div>

            <div class="hero-text-block">
                <p>
                At UniCou Learning Hub, we combine <strong>digital learning infrastructure, international academic partnerships</strong>, and <strong>career-aligned programs</strong> to help learners build skills, earn recognised qualifications, and prepare for global opportunitiesanytime, anywhere.
                </p>
            </div>

            <a href="#" class="hero-btn">
                INITIALIZE SYNC NODE →
            </a>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="hero-media">
            <img src="{{ asset('images/hero_Banner_img (1).png') }}" alt="Learning Hub Graphic">
            </div>

        </div>
    </div>
</section>

<section class="learning-platform padding_top_bot Sec2" id="Sec2" style="background-image: url('{{ asset('images/learning-platform.png') }}');">
    <div class="container">
        <div class="lp-container">

            <!-- LEFT CONTENT -->
            <div class="lp-content">
            <h2>
                A MODERN LEARNING PLATFORM BUILT FOR A<br>
                <span>GLOBAL AUDIENCE</span>
            </h2>

            <div class="lp-divider"></div>

            <p>
                Education is evolving, and UniCou Learning Hub was built to meet the
                needs of today’s learners. As a <strong>technology-driven learning
                platform</strong>, we provide structured access to academic programs,
                professional courses, and awarding body qualifications through an
                integrated digital environment.
            </p>

            <p>
                Our platform supports learners across <strong>Asia, Africa, Europe,
                the Middle East</strong>, and beyond, offering study options that are
                flexible, scalable, and aligned with international education standards.
            </p>

            <p>
                Whether you are a student preparing for university, a professional
                upgrading your skills, or an institution delivering programs digitally,
                UniCou Learning Hub provides the tools and pathways to succeed.
            </p>
            </div>

            <!-- RIGHT IMAGE -->
            <div class="lp-image">
            <img src="{{ asset('images/Untitled design.png') }}" alt="Global Network">
                <!-- <video autoplay="" muted="" loop="">
        <source src="./Untitled design (7).mp4" autoplay="" muted="" loop="" type="video/mp4"></video> -->
            </div>

        </div>
    </div>
</section>

<section class="study-section padding_top_bot Sec3" id="Sec3">
  <div class="container">

    <!-- SECTION TITLE -->
    <div class="section-head">
      <h2>WHAT YOU CAN STUDY AT UNICOU LEARNING HUB</h2>
      <span class="title-line"></span>
    </div>

    <!-- CARDS -->
    <div class="study-grid">

      <!-- CARD 1 -->
      <div class="study-card">
        <h3>
          Academic Programs –<br>
          Undergraduate & Postgraduate Pathways
        </h3>
        <span class="card-line"></span>

        <p>
          UniCou Learning Hub supports learners pursuing
          <strong>undergraduate and postgraduate academic programs</strong>
          through partner universities and education providers.
        </p>

        <p>These programs are designed to:</p>
        <ul>
          <li>Build strong academic foundations</li>
          <li>Support progression to on-campus or international study</li>
          <li>Align with global university entry requirements</li>
        </ul>

        <p>
          Academic pathways may include foundation routes,
          diploma-to-degree progression, and direct entry preparation
          for international universities.
        </p>
      </div>

      <!-- CARD 2 -->
      <div class="study-card">
        <h3>
          Professional & Career-Focused Courses
        </h3>
        <span class="card-line"></span>

        <p>
          In addition to academic education, UniCou Learning Hub offers
          <strong>professional and skills-based courses</strong> tailored
          to today’s job market.
        </p>

        <p>These programs focus on:</p>
        <ul>
          <li>Practical, industry-relevant skills</li>
          <li>Career advancement and employability</li>
          <li>Short-term and modular learning options</li>
        </ul>

        <p>
          Professional courses are ideal for working professionals,
          graduates, and learners seeking upskilling without committing
          to full-time academic study.
        </p>
      </div>

      <!-- CARD 3 -->
      <div class="study-card">
        <h3>
          Hybrid & Blended Learning Models
        </h3>
        <span class="card-line"></span>

        <p>
          UniCou Learning Hub supports <strong>hybrid education</strong>,
          combining:
        </p>

        <ul>
          <li>Online learning delivery</li>
          <li>Instructor-led sessions</li>
          <li>Partner-centre or campus engagement</li>
        </ul>

        <p>
          Hybrid models allow learners to <strong>study locally while
          earning internationally recognised qualifications</strong>,
          reducing costs while maintaining academic quality.
        </p>
      </div>

    </div>
  </div>
</section>

<section class="awarding-section padding_top_bot Sec4" id="Sec4">
  <div class="container">
    <div class="awarding-card" style="background-image: url('{{ asset('images/planeImg.png') }}');">

      <h2>
        INTERNATIONAL AWARDING BODIES & RECOGNISED QUALIFICATIONS
      </h2>
      <span class="title-line"></span>

      <p>
        We work with <strong>international awarding bodies and education partners</strong>
        to ensure that programs delivered through UniCou Learning Hub are:
      </p>

      <ul class="list">
        <li>Globally recognised</li>
        <li>Quality-assured</li>
        <li>
          Aligned with international frameworks such as CEFR and
          university credit systems
        </li>
      </ul>

      <p class="sub-title"><strong>These qualifications support:</strong></p>

      <ul class="list">
        <li>University progression</li>
        <li>Credit transfer opportunities</li>
        <li>Professional recognition across multiple countries</li>
      </ul>

    </div>
  </div>
</section>

<section class="exam-section padding_top_bot Sec5" id="Sec5">
    <div class="container">
        <h1>EXAM PREPARATION, ASSESSMENTS & LEARNING SUPPORT</h1>
        <p class="subtitle">
            UniCou Learning Hub also supports learners preparing for international English and academic exams,
            integrating learning resources with UniCou Ltd’s official exam services.
        </p>

        <h3>LEARNERS CAN ACCESS PREPARATION SUPPORT FOR:</h3>

        <div class="card-container">
            <div class="card">
                <img src="{{ asset('images/pearson.jpeg') }}" alt="PTE">
                <p class="small">PTE AUTHORIZED</p>
                <h4>PTE VOUCHER</h4>
            </div>

            <div class="card">
                <img src="{{ asset('images/ielts.jpeg') }}" alt="IELTS">
                <p class="small">IELTS AUTHORIZED</p>
                <h4>IELTS VOUCHER</h4>
            </div>

            <div class="card">
                <img src="{{ asset('images/toefl.jpeg') }}" alt="TOEFL">
                <p class="small">TOEFL AUTHORIZED</p>
                <h4>TOEFL iBT VOUCHER</h4>
            </div>

            <div class="card">
                <img src="{{ asset('images/language-cert.jpeg') }}" alt="LanguageCert">
                <p class="small">LanguageCert</p>
                <h4>LANGUAGECERT ENGLISH TEST</h4>
            </div>

            <div class="card">
                <img src="{{ asset('images/skills-for-eng.jpeg') }}" alt="Skills">
                <p class="small">Skills for English</p>
                <h4>SKILLS FOR ENGLISH TEST</h4>
            </div>
        </div>

        <p class="footer-text">
            Through UniCou Ltd, learners may also purchase official discounted exam vouchers,
            ensuring a seamless journey from learning to assessment.
        </p>

        <button class="btn">READ MORE →</button>
    </div>
</section>

<section class="tech-section padding_top_bot Sec6" id="Sec6">
    <div class="container">
        <h1>TECHNOLOGY–DRIVEN LEARNING EXPERIENCE</h1>
        <p class="subtitle">
            UniCou Learning Hub is powered by a modern Learning Management System (LMS) that enables
        </p>

        <div class="timeline-wrapper">
            <div class="timeline-path"></div>

            <div class="step step-1">
                <span>01</span>
                <p>Secure learner<br>accounts</p>
            </div>

            <div class="step step-2">
                <span>02</span>
                <p>Course access<br>and progress tracking</p>
            </div>

            <div class="step step-3">
                <span>03</span>
                <p>Digital learning<br>materials and assessments</p>
            </div>

            <div class="step step-4">
                <span>04</span>
                <p>Instructor interaction<br>and feedback</p>
            </div>

            <div class="step step-5">
                <span>05</span>
                <p>Scalable access<br>for institutions and partners</p>
            </div>
        </div>

        <p class="footer-text">
            Our platform is designed to support self-paced learning, instructor-led delivery,
            and blended education models, making learning accessible across time zones and regions.
        </p>
    </div>
</section>

<section class="who-section padding_top_bot Sec7" id="Sec7">
    <div class="container">
        <div class="who-container">

            <!-- LEFT CONTENT BOX -->
            <div class="who-content">
                <h2>WHO UNICOU LEARNING HUB IS FOR</h2>
                <p class="intro">UniCou Learning Hub is designed for</p>

                <ul>
                    <li>Students preparing for international education</li>
                    <li>Learners seeking academic progression pathways</li>
                    <li>Professionals upgrading skills or qualifications</li>
                    <li>Institutions delivering digital or hybrid programs</li>
                    <li>Training providers and awarding bodies</li>
                    <li>Agents and preparation centres supporting learners</li>
                </ul>
            </div>

            <!-- RIGHT IMAGE -->
            <div class="who-image">
                <img src="{{ asset('images/How-we-partner.png') }}" alt="Professional Meeting">
            </div>

        </div>
    </div>
</section>

<section class="section-wrapper padding_top_bot Sec8" id="Sec8">
    <div class="container">
        <div class="cards">
            
            <!-- Left Card -->
            <div class="card card-dark">
            <h2>Part of the UniCou Global Education Ecosystem</h2>
            <p>
                UniCou Learning Hub operates as part of the wider UniCou Ltd global
                education ecosystem, which includes:
            </p>
            <ul>
                <li>Study abroad and university admissions support</li>
                <li>Official English language testing and exam centres</li>
                <li>Immigration and global mobility services</li>
                <li>Corporate placement and institutional partnerships</li>
            </ul>
            <p>
                This integrated approach ensures learners receive end-to-end support,
                from learning and assessment to progression and placement.
            </p>
            </div>

            <!-- Right Card -->
            <div class="card card-light">
            <h2>Our Vision for Digital Learning</h2>
            <p>
                Our vision is to create a trusted global learning hub that connects
                learners with education, skills, and opportunity through technology.
            </p>
            <ul>
                <li>Expand access to international education</li>
                <li>Support lifelong learning and career development</li>
                <li>Empower institutions through digital delivery</li>
                <li>Build bridges between learning and global mobility</li>
            </ul>
            </div>

        </div>
    </div>
</section>

<section class="cta-section padding_top_bot Sec9" id="Sec9">
    <div class="container">
        <div class="cta-card">
            <h2>Start Learning with UniCou Learning Hub</h2>

            <p>
            Education should be flexible, accessible, and globally relevant. UniCou
            Learning Hub was built to support learners who want more than just
            content they want progression, recognition, and opportunity.
            </p>

            <p>
            Whether you are beginning your academic journey, upgrading professional
            skills, or preparing for international exams, UniCou Learning Hub is your
            digital gateway to global education.
            </p>

            <p><strong>Learn smarter. Study globally. Progress with confidence.</strong></p>

            <a href="#" class="cta-btn">
            LOGIN TO STUDY HUB <span>→</span>
            </a>
        </div>
    </div>
</section>

<footer class="footer padding_top_bot">
  <div class="footer-container">

    <!-- Logo & About -->
    <div class="footer-col brand">
      <h2 class="logo">
        <img src="https://tfpadvisory.bkhomesolutionindore.com/wp-content/uploads/2025/12/Untitled-design-2026-01-16T145219.133.png" alt="">
      </h2>
      <p>
        Secure your University Admission. Master your Exams with our LMS, and
        save on Official Vouchers for IELTS, PTE, TOEFL, LanguageCert, Duolingo,
        GRE and more.
      </p>

      <h4>Follow Us</h4>
      <div class="socials">
        <a href="#">
            <svg width="64px" height="64px" viewBox="38.657999999999994 12.828 207.085 207.085" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M158.232 219.912v-94.461h31.707l4.747-36.813h-36.454V65.134c0-10.658 2.96-17.922 18.245-17.922l19.494-.009V14.278c-3.373-.447-14.944-1.449-28.406-1.449-28.106 0-47.348 17.155-47.348 48.661v27.149H88.428v36.813h31.788v94.461l38.016-.001z" fill="#3c5a9a"></path></g></svg>
        </a>
        <a href="#">
            <svg width="64px" height="64px" viewBox="0 -4 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>Twitter-color</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Color-" transform="translate(-300.000000, -164.000000)" fill="#00AAEC"> <path d="M348,168.735283 C346.236309,169.538462 344.337383,170.081618 342.345483,170.324305 C344.379644,169.076201 345.940482,167.097147 346.675823,164.739617 C344.771263,165.895269 342.666667,166.736006 340.418384,167.18671 C338.626519,165.224991 336.065504,164 333.231203,164 C327.796443,164 323.387216,168.521488 323.387216,174.097508 C323.387216,174.88913 323.471738,175.657638 323.640782,176.397255 C315.456242,175.975442 308.201444,171.959552 303.341433,165.843265 C302.493397,167.339834 302.008804,169.076201 302.008804,170.925244 C302.008804,174.426869 303.747139,177.518238 306.389857,179.329722 C304.778306,179.280607 303.256911,178.821235 301.9271,178.070061 L301.9271,178.194294 C301.9271,183.08848 305.322064,187.17082 309.8299,188.095341 C309.004402,188.33225 308.133826,188.450704 307.235077,188.450704 C306.601162,188.450704 305.981335,188.390033 305.381229,188.271578 C306.634971,192.28169 310.269414,195.2026 314.580032,195.280607 C311.210424,197.99061 306.961789,199.605634 302.349709,199.605634 C301.555203,199.605634 300.769149,199.559408 300,199.466956 C304.358514,202.327194 309.53689,204 315.095615,204 C333.211481,204 343.114633,188.615385 343.114633,175.270495 C343.114633,174.831347 343.106181,174.392199 343.089276,173.961719 C345.013559,172.537378 346.684275,170.760563 348,168.735283" id="Twitter"> </path> </g> </g> </g></svg>
        </a>
        <a href="#">
            <svg width="64px" height="64px" viewBox="0 -2 44 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>LinkedIn-color</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Color-" transform="translate(-702.000000, -265.000000)" fill="#007EBB"> <path d="M746,305 L736.2754,305 L736.2754,290.9384 C736.2754,287.257796 734.754233,284.74515 731.409219,284.74515 C728.850659,284.74515 727.427799,286.440738 726.765522,288.074854 C726.517168,288.661395 726.555974,289.478453 726.555974,290.295511 L726.555974,305 L716.921919,305 C716.921919,305 717.046096,280.091247 716.921919,277.827047 L726.555974,277.827047 L726.555974,282.091631 C727.125118,280.226996 730.203669,277.565794 735.116416,277.565794 C741.21143,277.565794 746,281.474355 746,289.890824 L746,305 L746,305 Z M707.17921,274.428187 L707.117121,274.428187 C704.0127,274.428187 702,272.350964 702,269.717936 C702,267.033681 704.072201,265 707.238711,265 C710.402634,265 712.348071,267.028559 712.41016,269.710252 C712.41016,272.34328 710.402634,274.428187 707.17921,274.428187 L707.17921,274.428187 L707.17921,274.428187 Z M703.109831,277.827047 L711.685795,277.827047 L711.685795,305 L703.109831,305 L703.109831,277.827047 L703.109831,277.827047 Z" id="LinkedIn"> </path> </g> </g> </g></svg>
        </a>
      </div>
    </div>

    <!-- Accordion Column -->
    <div class="footer-col accordion">
      <button class="accordion-header">Verticals</button>
      <ul class="accordion-body">
        <li>Exam Vouchers</li>
        <li>LMS</li>
        <li>Admission Hub</li>
        <li>About Us</li>
        <li>Apply Now</li>
        <li>Career</li>
      </ul>
    </div>

    <div class="footer-col accordion">
      <button class="accordion-header">Legal & Policies</button>
      <ul class="accordion-body">
        <li>Privacy Policy</li>
        <li>Modern Slavery</li>
        <li>Accessibility</li>
        <li>Cookie Use Policy</li>
        <li>Whistleblowing Policy</li>
        <li>Carbon Reduction Plan</li>
        <li>Website Terms of Use</li>
      </ul>
    </div>

    <div class="footer-col accordion">
      <button class="accordion-header">Contact Info</button>
      <ul class="accordion-body">
        <li>connect@unicou.uk</li>
        <li>UK: Chepstow Avenue, Manchester M33 4QP</li>
        <li>Dubai: 24695 Deira, UAE</li>
        <li>Pakistan: Township, Lahore</li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    © 2026 UNICOU All rights reserved. Designed and Developed by Veva Technology
  </div>
</footer>
<script src="{{ asset('js/homeScript.js') }}"></script>
<script src="{{ asset('js/homeParticles.js') }}"></script>
</body>
</html>
