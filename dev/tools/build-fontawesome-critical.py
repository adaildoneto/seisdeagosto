#!/usr/bin/env python3
"""Gera o subconjunto WOFF2 dos ícones usados no primeiro viewport da home."""

from pathlib import Path

from fontTools import subset

ROOT = Path(__file__).resolve().parents[2]
FONT_DIR = ROOT / "assets" / "vendor" / "font-awesome-4.7" / "fonts"
SOURCE = FONT_DIR / "fontawesome-webfont.ttf"
TARGET = FONT_DIR / "fontawesome-critical.woff2"

# search, heart, star, clock-o, times e thumbs-up.
UNICODES = "U+F002,U+F004,U+F005,U+F00D,U+F017,U+F164"


def main() -> None:
    options = subset.Options()
    options.flavor = "woff2"
    options.layout_features = ["*"]
    options.name_IDs = ["*"]
    options.name_legacy = True
    options.notdef_outline = True
    options.recalc_timestamp = False

    font = subset.load_font(str(SOURCE), options)
    try:
        subsetter = subset.Subsetter(options=options)
        subsetter.populate(unicodes=subset.parse_unicodes(UNICODES))
        subsetter.subset(font)
        subset.save_font(font, str(TARGET), options)
    finally:
        font.close()

    print(f"{SOURCE.name}: {SOURCE.stat().st_size} -> {TARGET.stat().st_size} bytes")


if __name__ == "__main__":
    main()
