#!/usr/bin/env python3
"""Prints the WCAG 2 contrast of every palette colour on every other, from a theme.json.

    bin/contrast.py <theme.json or style variation .json> [--bg "#hex Name" ...] [--md]

Cells: ✓ 4.5:1 or more (any text) · L 3–4.49 (large text only: 24 px, or 18.66 px bold)
· ✗ under 3 (never for text; under 3 also fails for borders and icons).
--bg adds a background that isn't in the palette (a photo's tone, a sampled panel).
--md prints a Markdown table to paste into notes.
"""
import json
import sys
from pathlib import Path


def luminance(hex_colour):
    h = hex_colour.lstrip('#')
    if len(h) in (3, 4):
        h = ''.join(c * 2 for c in h)
    rgb = [int(h[i:i + 2], 16) / 255 for i in (0, 2, 4)]
    lin = [c / 12.92 if c <= 0.04045 else ((c + 0.055) / 1.055) ** 2.4 for c in rgb]
    return 0.2126 * lin[0] + 0.7152 * lin[1] + 0.0722 * lin[2]


def ratio(a, b):
    la, lb = sorted((luminance(a), luminance(b)), reverse=True)
    return (la + 0.05) / (lb + 0.05)


def mark(r):
    return '✓' if r >= 4.5 else 'L' if r >= 3 else '✗'


args = sys.argv[1:]
if not args:
    sys.exit(__doc__)
data = json.loads(Path(args[0]).expanduser().read_text())
palette = [(p['name'], p['color']) for p in data.get('settings', {}).get('color', {}).get('palette', [])
           if str(p.get('color', '')).startswith('#')]
if not palette:
    sys.exit('No hex colours in settings.color.palette.')
for colour in palette:
    if len(colour[1].lstrip('#')) == 8:
        print(f'Note: {colour[0]} {colour[1]} has transparency; its ratio depends on what is behind it.', file=sys.stderr)

backgrounds = list(palette)
i = 0
while i < len(args):
    if args[i] == '--bg' and i + 1 < len(args):
        hexv, _, name = args[i + 1].partition(' ')
        backgrounds.append((name or hexv, hexv))
        i += 2
    else:
        i += 1

texts = palette
if '--md' in args:
    print('| Background | ' + ' | '.join(f'{n} `{c}`' for n, c in texts) + ' |')
    print('|---' * (len(texts) + 1) + '|')
    for bn, bc in backgrounds:
        cells = ['—' if bc == tc else f'{mark(ratio(tc, bc))} {ratio(tc, bc):.2f}' for _, tc in texts]
        print(f'| {bn} `{bc}` | ' + ' | '.join(cells) + ' |')
else:
    width = max(len(n) for n, _ in backgrounds) + 10
    print('text →'.ljust(width) + ''.join(n[:14].ljust(16) for n, _ in texts))
    for bn, bc in backgrounds:
        row = f'{bn} {bc}'.ljust(width)
        for _, tc in texts:
            row += ('—' if bc == tc else f'{mark(ratio(tc, bc))} {ratio(tc, bc):5.2f}').ljust(16)
        print(row)
