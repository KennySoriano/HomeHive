<style>
:root {
  --hh-sunset-amber: #FB8C00;
  --hh-rich-gold: #F57C00;
  --hh-bee-brown: #5D4037;
  --hh-cream-white: #FFF8E1;
  --hh-golden-shadow: #FFCC80;
  --hh-soft-honey: #FFE0B2;
}

/* Footer styles that will work when included */
.modern-footer {
  background-color: var(--hh-bee-brown);
  color: var(--hh-cream-white);
  padding: 2rem;
  font-family: 'Inter', sans-serif;
  width: 100%;
}

/* This ensures the footer container centers content */
.modern-footer .footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  flex-wrap: wrap;
}

/* Rest of your existing footer styles remain the same */
.modern-footer .footer-main {
  display: flex;
  align-items: center;
  gap: 3rem;
}

.modern-footer .footer-section {
  display: flex;
  flex-direction: column;
}

.modern-footer .footer-section h3 {
  color: var(--hh-soft-honey);
  margin-bottom: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  white-space: nowrap;
}

.modern-footer .footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  gap: 1.5rem;
}

.modern-footer .footer-section ul li {
  margin: 0;
}

.modern-footer .footer-section ul li a {
  color: var(--hh-golden-shadow);
  text-decoration: none;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.modern-footer .footer-section ul li a:hover {
  color: var(--hh-cream-white);
}

.modern-footer .social-links {
  display: flex;
  gap: 1rem;
}

.modern-footer .social-links a {
  color: var(--hh-soft-honey);
  font-size: 1.2rem;
  transition: all 0.3s ease;
}

.modern-footer .social-links a:hover {
  transform: translateY(-2px);
  color: var(--hh-cream-white);
}

.modern-footer .footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding-top: 1.5rem;
  margin-top: 1.5rem;
  border-top: 1px solid rgba(255, 248, 225, 0.2);
  font-size: 0.9rem;
  color: var(--hh-soft-honey);
}

.modern-footer .legal-links {
  display: flex;
  gap: 1.2rem;
}

.modern-footer .legal-links a {
  color: var(--hh-golden-shadow);
  text-decoration: none;
  transition: color 0.3s ease;
}

.modern-footer .legal-links a:hover {
  color: var(--hh-cream-white);
}

@media (max-width: 900px) {
  .modern-footer .footer-main {
    gap: 1.5rem;
  }
  
  .modern-footer .footer-section ul {
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .modern-footer .footer-container {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .modern-footer .footer-main {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    width: 100%;
  }
  
  .modern-footer .footer-section ul {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .modern-footer .footer-bottom {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
}

/* Add this to any page that includes the footer */
.page-with-footer {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.page-with-footer main {
  flex: 1;
}
</style>

<footer class="modern-footer">
  <div class="footer-container">
    <div class="footer-main">
      <div class="footer-section">
        <h3>HomeHive</h3>
        <ul>
          <li><a href="#">About</a></li>
          <li><a href="#">Our Mission</a></li>
          <li><a href="#">Careers</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>Contact</h3>
        <ul>
          <li><a href="#">Support</a></li>
          <li><a href="#">Email Us</a></li>
          <li><a href="#">FAQs</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>Socials</h3>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>

   <div class="footer-bottom">
  <p>© 2025 HomeHive. Academic use only. No data collection or malicious activities. All rights reserved.</p>
  <div class="legal-links">
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
  </div>
</div>

    </div>
  </div>
</footer>