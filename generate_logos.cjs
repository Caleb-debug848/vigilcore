const fs = require('fs');
const path = require('path');
const { Resvg } = require('@resvg/resvg-js');

const imagesDir = path.join(__dirname, 'public', 'images');
const diagramsDir = path.join(__dirname, 'diagrams');

// 1. SVG Icône seul
const iconSvgPath = path.join(imagesDir, 'logo.svg');
const iconSvgContent = fs.readFileSync(iconSvgPath, 'utf-8');

// 2. SVG Complet horizontal (Fond Clair / Transparent)
const fullSvgPath = path.join(imagesDir, 'logo_vigilcore_full.svg');
const fullSvgContent = fs.readFileSync(fullSvgPath, 'utf-8');

// SVG Complet horizontal (Version Fond Sombre)
const fullSvgDark = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 120" fill="none">
    <defs>
        <linearGradient id="shieldGradDark" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3B82F6" />
            <stop offset="50%" stop-color="#2563EB" />
            <stop offset="100%" stop-color="#1D4ED8" />
        </linearGradient>
        <linearGradient id="amberSparkDark" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#FCD34D" />
            <stop offset="100%" stop-color="#F59E0B" />
        </linearGradient>
        <filter id="logoGlowDark" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#3B82F6" flood-opacity="0.4" />
        </filter>
    </defs>
    <rect width="520" height="120" rx="16" fill="#0F172A"/>
    <g transform="translate(15, 10)" filter="url(#logoGlowDark)">
        <path d="M50 8L86 22V52C86 73 70 90 50 96C30 90 14 73 14 52V22L50 8Z" fill="url(#shieldGradDark)" stroke="#60A5FA" stroke-width="2.5" stroke-linejoin="round" />
        <path d="M50 20L74 31V50C74 65 63 77 50 82C37 77 26 65 26 50V31L50 20Z" fill="#020617" fill-opacity="0.8" stroke="#FFFFFF" stroke-width="1.8" stroke-opacity="0.9" />
        <path d="M36 38L50 68L64 38" stroke="#93C5FD" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M32 50H68" stroke="#BAE6FD" stroke-width="1.5" stroke-dasharray="2 2" stroke-opacity="0.8" />
        <path d="M50 42L52.5 47.5L58 50L52.5 52.5L50 58L47.5 52.5L42 50L47.5 47.5L50 42Z" fill="url(#amberSparkDark)" />
        <circle cx="50" cy="50" r="2.2" fill="#FFFFFF" />
    </g>
    <g transform="translate(130, 0)">
        <text x="0" y="62" font-family="'Segoe UI', sans-serif" font-size="44" font-weight="900" letter-spacing="-0.5" fill="#FFFFFF">
            VIGIL<tspan fill="#60A5FA">CORE</tspan>
        </text>
        <rect x="0" y="76" width="365" height="2" rx="1" fill="#334155" />
        <rect x="0" y="76" width="90" height="2" rx="1" fill="#38BDF8" />
        <text x="0" y="98" font-family="'Segoe UI', sans-serif" font-size="12.5" font-weight="700" letter-spacing="2.5" fill="#94A3B8">
            PROACTIVE MONITORING &amp; FORENSICS
        </text>
    </g>
</svg>`;

// Fonction de rendu PNG haute résolution
function renderToPng(svgStr, outputPath, width, background = null) {
    let finalSvg = svgStr;
    if (background) {
        if (!finalSvg.includes(`<rect width="100%" height="100%"`)) {
            finalSvg = finalSvg.replace(/(<svg[^>]*>)/i, `$1<rect width="100%" height="100%" fill="${background}"/>`);
        }
    }
    const resvg = new Resvg(finalSvg, {
        fitTo: { mode: 'width', value: width },
        font: {
            loadSystemFonts: true,
            defaultFontFamily: 'Segoe UI'
        }
    });
    const pngData = resvg.render();
    const pngBuffer = pngData.asPng();
    fs.writeFileSync(outputPath, pngBuffer);
    console.log(`✓ Généré : ${outputPath} (${width}px - ${(pngBuffer.length / 1024).toFixed(1)} KB)`);
}

console.log('--- Génération des logos VigilCore en Haute Définition ---');

// 1. Icônes carrées (2048x2048)
renderToPng(iconSvgContent, path.join(imagesDir, 'logo_vigilcore_transparent.png'), 2048);
renderToPng(iconSvgContent, path.join(imagesDir, 'logo_vigilcore_white_bg.png'), 2048, '#FFFFFF');
renderToPng(iconSvgContent, path.join(imagesDir, 'logo_vigilcore_dark_bg.png'), 2048, '#0F172A');
renderToPng(iconSvgContent, path.join(imagesDir, 'logo.png'), 1024);

// Copie également dans diagrams/ pour accès direct
renderToPng(iconSvgContent, path.join(diagramsDir, 'logo_vigilcore.png'), 2048);

// 2. Logos Complets Horizontaux (Typographie + Icône) (3120 x 720)
renderToPng(fullSvgContent, path.join(imagesDir, 'logo_vigilcore_full_transparent.png'), 3120);
renderToPng(fullSvgContent, path.join(imagesDir, 'logo_vigilcore_full_white_bg.png'), 3120, '#FFFFFF');
renderToPng(fullSvgDark, path.join(imagesDir, 'logo_vigilcore_full_dark_bg.png'), 3120);
renderToPng(fullSvgContent, path.join(diagramsDir, 'logo_vigilcore_full.png'), 3120, '#FFFFFF');

console.log('Tous les logos ont été générés avec succès !');
