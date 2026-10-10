# Bantay Baha Flood Monitor

Isang web application para sa pag-monitor ng lebel ng tubig baha sa barangay, kumpleto sa live data chart at AI-generated public advisories.

## Paano Papatakbuhin (Local)
1. I-clone ang repository na ito.
2. Buksan ang terminal at patakbuhin ang PHP built-in server: `php -S localhost:8000`
3. Sa ibang terminal window, patakbuhin ang simulator: `node simulator.js` (siguraduhing may naka-install na Node.js).
4. Buksan ang `http://localhost:8000/index.html` sa iyong web browser.

## AI Prompt na Ginamit (Gemini)
"Write a short advisory (max 3 sentences) in Filipino and English for residents. Data: Average water level is [AVG]cm, Trend is [TREND]. Normal is <100cm, Alert is 100-149cm, Danger is 150cm+."

## Deployment Notes
Kung hindi gumana ng maayos ang deployment sa free hosting, ito ay dahil sa server limits ng libreng PHP at SQLite environment. Nakatago nang ligtas ang GEMINI_API_KEY sa environment variables ng server kaya walang nakalabas na susi sa public code na ito.
