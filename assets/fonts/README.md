Self-hosted webfonts

Place .woff2 and .woff files here using the expected filenames:

Open Sans (assets/fonts/open-sans):
- OpenSans-Light.woff2 / .woff
- OpenSans-LightItalic.woff2 / .woff
- OpenSans-Regular.woff2 / .woff
- OpenSans-Italic.woff2 / .woff
- OpenSans-SemiBold.woff2 / .woff
- OpenSans-SemiBoldItalic.woff2 / .woff
- OpenSans-Bold.woff2 / .woff
- OpenSans-BoldItalic.woff2 / .woff

Lato (assets/fonts/lato):
- Lato-Light.woff2 / .woff
- Lato-LightItalic.woff2 / .woff
- Lato-Regular.woff2 / .woff
- Lato-Italic.woff2 / .woff
- Lato-Bold.woff2 / .woff
- Lato-BoldItalic.woff2 / .woff

Notes:
- The theme enqueues css/fonts-local.css which declares @font-face for these names.
- Ensure file names match exactly or update css/fonts-local.css accordingly.
- Prefer WOFF2; WOFF is included as fallback. Use font-display: swap to avoid FOIT.
