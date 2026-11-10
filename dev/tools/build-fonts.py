#!/usr/bin/env python3
"""Gera os arquivos WOFF2 usados pelo tema a partir dos TTF de assets/fonts.

Uso:
    python dev/tools/build-fonts.py

Os arquivos .woff2 ficam ao lado dos .ttf e são os que o tema carrega.
Os TTF continuam no repositório como fonte/fallback.
"""

from pathlib import Path

from fontTools import subset

ROOT = Path(__file__).resolve().parents[2]
FONTS = ROOT / "assets" / "fonts"

# Latin + Latin Extended-A (cobre acentos do português e a maioria dos nomes
# próprios usados no portal) + pontuação tipográfica.
UNICODES = (
    "U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,"
    "U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,"
    "U+2212,U+2215,U+FEFF,U+FFFD"
)

FILES = [
    "open-sans/OpenSans-Variable.ttf",
    "open-sans/OpenSans-Italic-Variable.ttf",
    "lato/Lato-Light.ttf",
    "lato/Lato-LightItalic.ttf",
    "lato/Lato-Regular.ttf",
    "lato/Lato-Italic.ttf",
    "lato/Lato-Bold.ttf",
    "lato/Lato-BoldItalic.ttf",
]

def build(relative: str) -> None:
    source = FONTS / relative
    target = source.with_suffix(".woff2")
    if not source.exists():
        raise SystemExit(f"Fonte não encontrada: {source}")

    options = subset.Options()
    options.flavor = "woff2"
    options.layout_features = ["*"]
    options.name_IDs = ["*"]
    options.name_legacy = True
    options.notdef_outline = True
    options.recalc_bounds = True
    options.recalc_timestamp = False

    font = subset.load_font(str(source), options)
    try:
        subsetter = subset.Subsetter(options=options)
        subsetter.populate(unicodes=subset.parse_unicodes(UNICODES))
        subsetter.subset(font)
        subset.save_font(font, str(target), options)
    finally:
        font.close()

    before = source.stat().st_size / 1024
    after = target.stat().st_size / 1024
    print(f"{relative:48} {before:8.1f} KB -> {after:7.1f} KB")


if __name__ == "__main__":
    for entry in FILES:
        build(entry)
