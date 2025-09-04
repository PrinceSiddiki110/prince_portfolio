<?php
require_once 'db.php';

// Group skills by category using mysqli
$skills = [];
$res = $mysqli->query("SELECT * FROM skills ORDER BY sort_order");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $category = strtolower(str_replace(' ', '_', $row['category']));
    $skills[$category][] = $row;
  }
  $res->free();
} else {
  $skills = [];
}

// Fetch projects
$projects = [];
$res = $mysqli->query("SELECT * FROM projects ORDER BY created_at DESC");
if ($res) {
  $projects = $res->fetch_all(MYSQLI_ASSOC);
  $res->free();
}

// Fetch education
$education = [];
$res = $mysqli->query("SELECT * FROM education ORDER BY sort_order");
if ($res) {
  $education = $res->fetch_all(MYSQLI_ASSOC);
  $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Prince — Portfolio</title>
  <meta name="description" content="Nure Alam Siddiki Prince — Computer Science Student, ML Enthusiast & Web Developer from Chuadanga, Bangladesh.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/navigation.css">
  <link rel="stylesheet" href="css/about.css">
  <link rel="stylesheet" href="css/skills.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="stylesheet" href="css/education.css">
  <link rel="stylesheet" href="css/contact.css">
  <link rel="stylesheet" href="css/responsive.css">
  <script src="https://kit.fontawesome.com/a2c8f1a2a0.js" crossorigin="anonymous" defer></script>
</head>

<body data-theme="light">
    <!-- Navigation Bar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <!-- Logo/Brand -->
            <div class="nav-brand">
                <div class="brand-avatar">P</div>
                <div class="brand-text">
                    <h1 class="brand-name">Nure A. S. <span class="surname">Prince</span></h1>
                    <p class="brand-role">CSE Student • ML Enthusiast • Web Dev</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="nav-menu" id="nav-menu">
                <a href="#home" class="nav-link active" data-link>
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
                <a href="#about" class="nav-link" data-link>
                    <i class="fa-solid fa-user"></i>
                    <span>About</span>
                </a>
                <a href="#skills" class="nav-link" data-link>
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span>Skills</span>
                </a>
                <a href="#projects" class="nav-link" data-link>
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Projects</span>
                </a>
                <a href="#education" class="nav-link" data-link>
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Education</span>
                </a>
                <a href="#contact" class="nav-link" data-link>
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contact</span>
                </a>
            </div>

            <!-- Right Side Actions -->
            <div class="nav-actions">
                <button id="themeToggle" class="btn btn-theme" aria-pressed="false" title="Toggle theme">
                    <i class="fa-solid fa-sun theme-icon sun"></i>
                    <i class="fa-solid fa-moon theme-icon moon"></i>
                </button>

                <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation menu">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>

    <!-- Main Content -->
    <main class="main-content" id="content">
            <header class="topbar">
                <div class="topbar-left">
                    <button id="mobileMenu" class="mobile-menu" aria-label="Open navigation menu" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle sidebar">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>
                <div class="topbar-right">
                    <div class="top-actions">
                        <button id="contactQuick" class="btn small ghost">
                            <i class="fa-solid fa-envelope"></i> Contact
                        </button>
                    </div>
                </div>
            </header>

      <section id="home" class="section hero reveal" tabindex="-1" aria-label="Home">
        <div class="hero-inner">
          <div class="left">
            <h2 class="hello">Hello — I'm</h2>
            <h1 class="title">Nure Alam Siddiki <span class="accent">Prince</span></h1>
            <p class="subtitle">Undergrad CSE Student • Machine Learning Enthusiast • Web Developer</p>
            <p class="tagline">I love exploring — from algorithms to new places. I build practical web apps and learn ML to solve real problems.</p>
            <div class="cta">
              <a href="#projects" class="btn primary">See Projects</a>
              <a href="#contact" class="btn ghost">Get in Touch</a>
            </div>
            <div class="quick-stats">
              <div class="stat"><strong><?php echo count($projects); ?></strong><span>Major Projects</span></div>
              <div class="stat"><strong>3</strong><span>Years of Study</span></div>
              <div class="stat"><strong><?php echo array_sum(array_map('count', $skills)); ?></strong><span>Technologies</span></div>
            </div>
          </div>
          <div class="right">
            <div class="profile-card" aria-hidden="true">
              <div class="profile-blob"></div>
              <img src="assets/img/profile-placeholder.png" alt="Profile placeholder" />
              <div class="profile-info">
                <h3>Prince</h3>
                <p>Chuadanga, Bangladesh</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="about" class="section reveal" aria-label="About">
        <h2 class="section-title">About Me</h2>
        <div class="about-grid">
          <div class="about-card">
            <p><strong>Nure Alam Siddiki Prince</strong> — an undergraduate Computer Science & Engineering student at <strong>Khulna University of Engineering & Technology (KUET)</strong>, batch 2021. Originally from <strong>Chuadanga, Bangladesh</strong>, I am a practicing <strong>Muslim</strong> who combines curiosity with discipline to learn and build.</p>
            <p>I focus on <strong>Machine Learning</strong> and <strong>Web Development</strong>. My interests also include travelling, discovering local foods, meeting new people, and applying computing to practical problems. I enjoy collaborating, leading small teams, and delivering functional, well-documented projects.</p>
            <div class="badges">
              <span class="badge">ML Enthusiast</span>
              <span class="badge">Web Developer</span>
              <span class="badge">Team Lead</span>
            </div>
          </div>
          <div class="about-highlights">
            <div class="highlight"><i class="fa-solid fa-location-dot"></i>
              <div><strong>Location : </strong><span>Chuadanga, Bangladesh</span></div>
            </div>
            <div class="highlight"><i class="fa-solid fa-mosque"></i>
              <div><strong>Faith : </strong><span>Muslim</span></div>
            </div>
            <div class="highlight"><i class="fa-solid fa-compass"></i>
              <div><strong>Hobbies : </strong><span>Traveling & Food</span></div>
            </div>
            <div class="highlight"><i class="fa-solid fa-people-group"></i>
              <div><strong>Soft Skills : </strong><span>Leadership, Communication</span></div>
            </div>
          </div>
        </div>
      </section>

      <section id="skills" class="section reveal" aria-label="Skills">
        <h2 class="section-title">Skills</h2>
        <div class="skills-grid">
          <?php 
          $categoryTitles = [
            'programming' => 'Programming Languages',
            'web_development' => 'Web Development',
            'database' => 'Database',
            'tools' => 'Tools & Technologies',
            'machine_learning' => 'Machine Learning'
          ];
          
          foreach ($skills as $category => $categorySkills): 
            if (empty($categorySkills)) continue;
            $title = $categoryTitles[$category] ?? ucfirst(str_replace('_', ' ', $category));
          ?>
            <div class="skill-card">
              <h3><?php echo $title; ?></h3>
              <?php foreach ($categorySkills as $skill): ?>
                <div class="skill">
                  <label><?php echo htmlspecialchars($skill['name']); ?></label>
                  <div class="bar">
                    <div class="bar-fill" style="--p:<?php echo $skill['level']; ?>%;"><?php echo $skill['level']; ?>%</div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>

          <div class="skill-card soft">
            <h3>Soft Skills</h3>
            <ul class="soft-list">
              <li>Leadership — lead university project teams</li>
              <li>Teamwork — collaborative developer & mentor</li>
              <li>Communication — technical documentation & presentation</li>
            </ul>
          </div>
        </div>
      </section>

      <section id="projects" class="section reveal" aria-label="Projects">
        <h2 class="section-title">Projects</h2>
        <div class="projects-controls">
          <div class="filters">
            <button class="filter active" data-filter="all">All</button>
            <?php
            $types = array_unique(array_column($projects, 'type'));
            foreach ($types as $type):
            ?>
              <button class="filter" data-filter="<?php echo htmlspecialchars($type); ?>"><?php echo ucfirst(htmlspecialchars($type)); ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="projects-grid" id="projectsGrid">
          <?php foreach ($projects as $project): ?>
            <article class="project-card" data-type="<?php echo htmlspecialchars($project['type']); ?>">
              <div class="thumb">
                <?php if (!empty($project['image']) && file_exists('assets/img/projects/' . $project['image'])): ?>
                  <img src="assets/img/projects/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                <?php elseif (!empty($project['image']) && file_exists('assets/img/' . $project['image'])): ?>
                  <img src="assets/img/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                <?php else: ?>
                  <img src="" alt="<?php echo htmlspecialchars($project['title']); ?>" style="display: none;">
                <?php endif; ?>
              </div>
              <div class="project-content">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <div class="project-meta">
                  <?php
                  $tagArray = explode(',', $project['tags'] ?? '');
                  foreach ($tagArray as $tag):
                    if (trim($tag)):
                  ?>
                      <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                  <?php
                    endif;
                  endforeach;
                  ?>
                </div>
                <div class="project-actions">
                  <?php if (!empty($project['github_url'])): ?>
                    <a class="btn small" href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" rel="noopener">
                      <i class="fa-brands fa-github"></i> GitHub
                    </a>
                  <?php endif; ?>
                  <button class="btn small outline" data-open="modal-<?php echo $project['slug']; ?>">Details</button>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="education" class="section reveal" aria-label="Education">
        <h2 class="section-title">Education</h2>
        <div class="edu-timeline">
          <?php foreach ($education as $edu): ?>
            <div class="edu-item">
              <div class="dot"></div>
              <div class="edu-card">
                <h3><?php echo htmlspecialchars($edu['institution']); ?></h3>
                <p><?php echo htmlspecialchars($edu['degree']); ?> — <?php echo $edu['start_year']; ?> – <?php echo $edu['end_year']; ?>
                  <?php if (!empty($edu['description'])): ?> — <?php echo htmlspecialchars($edu['description']); ?><?php endif; ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="contact" class="section reveal" aria-label="Contact">
        <h2 class="section-title">Contact & Connect</h2>
        <div class="contact-grid">
          <form id="contactForm" class="contact-form" method="post" action="contact_process.php" aria-label="Contact form">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" placeholder="Your full name" required>
            <label for="fromEmail">Your Email</label>
            <input type="email" id="fromEmail" name="fromEmail" placeholder="you@example.com" required>
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="Short subject" required>
            <label for="message">Your Message</label>
            <textarea id="message" name="message" rows="6" placeholder="Write your message..." required></textarea>
            <div class="form-actions">
              <button type="submit" class="btn primary">Send Message</button>
              <button type="button" id="copyEmail" class="btn small">Copy Email</button>
            </div>
            <p class="form-note">Your message will be saved and I'll get back to you soon.</p>
          </form>
          <aside class="contact-links" aria-label="Other contacts">
            <a href="https://wa.me/your-number" class="contact-link" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span></a>
            <a href="https://www.linkedin.com/in/your-profile" class="contact-link" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin"></i><span>LinkedIn</span></a>
            <a href="mailto:your-email@example.com" class="contact-link"><i class="fa-solid fa-envelope"></i><span>Email</span></a>
            <a href="assets/resume/Prince_Resume.pdf" class="contact-link download-cv" download>
              <i class="fa-solid fa-download"></i>
              <span>Download CV</span>
            </a>
            <div class="social-row">
              <a href="#" class="social small"><i class="fa-brands fa-github"></i></a>
              <a href="#" class="social small"><i class="fa-brands fa-facebook"></i></a>
              <a href="#" class="social small"><i class="fa-brands fa-instagram"></i></a>
            </div>
          </aside>
        </div>
      </section>
    </main>
    </div>

    <script src="js/script.js"></script>
</body>

</html>