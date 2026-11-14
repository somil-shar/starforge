<div align="center">

  <h1>STARFORGE</h1>

  <p><strong>Turn your code into projects worth starring.</strong></p>
  <p>Instant 10-point GitHub repo health audits, searchable README studio with 39+ licenses, and SEO discoverability tools.</p>

  <p>
    <a href="https://starforge.free.je" target="_blank">
      <img src="https://img.shields.io/badge/Live%20Demo-starforge.free.je-8257FF?style=for-the-badge&logo=googlechrome&logoColor=white" alt="Live Demo">
    </a>
  </p>

  <p>
    <a href="#license"><img src="https://img.shields.io/badge/license-MIT-8257FF.svg" alt="License: MIT"></a>
    <img src="https://img.shields.io/badge/PHP-7.4%2B%20%2F%208.x-777BB4?logo=php&logoColor=white" alt="PHP 7.4+">
    <img src="https://img.shields.io/badge/Database-Zero%20Config%20(Stateless)-10B981" alt="Zero DB Required">
    <img src="https://img.shields.io/badge/PRs-welcome-22D3EE.svg" alt="PRs Welcome">
  </p>

</div>

---

## 🌟 Overview

Most open-source repositories get overlooked not because the code is bad, but because the setup is unclear: no quickstart commands, vague descriptions, missing licenses, or unindexed topics. 

**StarForge** is a fast, developer-first web application designed to help builders and maintainers turn raw code into projects that people can actually install, understand, and star.

### 🛡️ Ethical Guarantee
StarForge **never** facilitates artificial stars, engagement bots, or follow-for-follow rings. Real stars come when developers try your project and it works out of the box.

---

## 🛠️ Core Tools

### 1. 🔍 Repository Health Analyzer (`/dashboard/analyzer.php`)
- **10-Point Diagnostic Engine:** Audits README depth, description clarity, topic coverage, open-source licensing, project structure, community governance, recent commit activity, and visual presentation.
- **Actionable Fixes:** Provides prioritized recommendations (High/Medium/Low) detailing exactly what needs to be added or polished.
- **Direct GitHub REST API Integration:** Fetches repository metadata and raw markdown in real time.

### 2. 📝 Interactive README Studio (`/dashboard/readme-generator.php`)
- **Searchable GitHub License Picker:** Search and select from **all 39 official GitHub and SPDX open-source licenses** (MIT, Apache 2.0, GPLv3, BSD, Mozilla, Unlicense, CC0, etc.) with real-time filtering and category chips.
- **Live Split Editor:** Edit markdown on the left and see GitHub-styled preview update in real time on the right.
- **Copy & Export:** One-click clipboard copying or direct `.md` file download.

### 3. 🚀 GitHub SEO & Discoverability Assistant (`/dashboard/seo-assistant.php`)
- **Topic Tag Generator:** Recommends high-intent tags and keywords tailored to your tech stack.
- **Search Snippet Previews:** Simulates how your repository appears in GitHub search results and social cards.
- **Character Count Optimizer:** Keeps descriptions concise and search-friendly.

---

## ⚡ Tech Stack & Architecture

- **Backend:** PHP 7.4+ / 8.x (Native, lightweight, no bulky frameworks)
- **Frontend:** Vanilla JavaScript, Modern CSS Custom Properties, Dark Mode UI
- **Zero-Database Mode:** Runs completely statelessly in-memory without MySQL or phpMyAdmin setup
- **Security:** Server-side GitHub Personal Access Token (PAT) protection prevents client credential leakage

---

## 🚀 Quickstart & Local Installation

### Requirements
- PHP 7.4 or higher with `curl` and `json` extensions
- Web server (Apache, Nginx, or PHP built-in server)

### 1. Run with PHP Built-in Server (Fastest)
```bash
git clone https://github.com/username/starforge.git
cd starforge
php -S localhost:8000
```
Open [http://localhost:8000](http://localhost:8000) in your browser.

### 2. Run with XAMPP / WAMP
1. Copy the `starforge` folder into your web root:
   ```text
   C:\xampp\htdocs\starforge
   ```
2. Start Apache in the XAMPP Control Panel (MySQL is **not** required).
3. Open [http://localhost/starforge](http://localhost/starforge) in your browser.

### 3. Optional: Add a GitHub Personal Access Token
By default, unauthenticated GitHub API requests are limited by GitHub to 60 requests per hour per IP. To raise your limit to 5,000 requests per hour, open `config/config.php` and set your token:
```php
define('GITHUB_API_TOKEN', 'ghp_yourPersonalAccessTokenHere');
```

---

## 📁 Project Structure

```text
starforge/
├── api/
│   ├── analyze.php          # Repo audit endpoint
│   ├── readme.php           # README generation endpoint
│   └── seo.php              # SEO suggestions endpoint
├── assets/
│   ├── css/style.css        # Dark mode design system & animations
│   └── js/                  # Real-time preview & interactive scripts
├── config/
│   ├── config.php           # App configuration & GitHub PAT
│   └── database.php         # Zero-database architecture stub
├── dashboard/
│   ├── analyzer.php         # Repo health audit page
│   ├── readme-generator.php # Searchable README builder
│   └── seo-assistant.php    # SEO & topic assistant
├── includes/
│   ├── analysis_engine.php  # 10-point evaluation engine
│   ├── github_service.php   # cURL GitHub REST API service
│   ├── licenses.php         # 39 official GitHub licenses directory
│   ├── header.php           # Header layout component
│   └── footer.php           # Footer layout component
├── index.php                # Landing page
└── README.md                # Project documentation
```

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the **MIT License**. See [`LICENSE`](LICENSE) for more information.

---

<div align="center">
  <sub>Built with care for open-source developers worldwide.</sub>
</div>
