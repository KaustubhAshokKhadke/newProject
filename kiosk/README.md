## Maharashtra Yojana Kiosk (PHP + Bootstrap)

Features:
- Video buttons to play scheme intro videos
- After video ends, show related FAQs (accordion)
- Manual chatbot modal with predefined Maharashtra Government Yojana questions
- Marathi answers using OpenAI ChatGPT (fallback works without API key)

### Requirements
- PHP 8.0+
- Internet for CDN assets; optional OpenAI API key

### Setup
1. Copy env template and set key (optional):
```
cp .env.example .env
echo "OPENAI_API_KEY=sk-..." >> .env
```
2. Place your videos:
```
mkdir -p assets/videos
# Add files:
# assets/videos/arogya.mp4
# assets/videos/shikshan.mp4
# assets/videos/krushi.mp4
```
3. Serve the app (choose one):
```
php -S 0.0.0.0:8080 -t .
```
Open `http://localhost:8080`.

### Files
- `index.php` UI (Bootstrap), video player, FAQs, chatbot modal
- `assets/app.js` client logic (video, FAQ render, chatbot calls)
- `assets/styles.css` small styles
- `chatbot.php` OpenAI integration endpoint
- `config.php` .env loader

### Notes
- If `OPENAI_API_KEY` is not set, the chatbot returns a safe Marathi fallback.
- Extend predefined questions in `index.php` inside `#predefinedQuestions`.
- Map FAQs per video in `assets/app.js` under `faqByVideo`.

