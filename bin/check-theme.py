#!/usr/bin/env python3
"""Checks a block theme against the kit's standards. Read-only.

    bin/check-theme.py <theme-dir> [--content <dir of .html page sources>]

Fails on what breaks the editor or the person editing; warns on what's worth a look.
Rules come from tudotype.com (notes in block markup break the editor), Team51's
html-to-block-theme standards (named groups, post content at content width, no
front-page.html) and WordPress's Ipsum theme (presets, never literals).
Last line: CHECK_OK or CHECK_FAIL.
"""
import json
import re
import sys
from pathlib import Path

args = sys.argv[1:]
if not args:
    sys.exit(__doc__)
theme = Path(args[0]).expanduser()
content_dir = Path(args[args.index('--content') + 1]).expanduser() if '--content' in args else None

fails, warns = [], []


def fail(where, msg):
    fails.append(f'FAIL  {where}: {msg}')


def warn(where, msg):
    warns.append(f'WARN  {where}: {msg}')


if not (theme / 'style.css').exists() or not (theme / 'templates' / 'index.html').exists():
    sys.exit(f'{theme} is not a block theme (style.css and templates/index.html are required).')

slug = theme.name
style = (theme / 'style.css').read_text()
text_domain = re.search(r'^Text Domain:\s*(\S+)', style, re.M)
text_domain = text_domain.group(1) if text_domain else slug

# theme.json
try:
    tj = json.loads((theme / 'theme.json').read_text())
except Exception as e:  # WordPress silently ignores a theme.json that doesn't parse.
    tj = {}
    fail('theme.json', f'does not parse ({e}); WordPress would ignore it')

settings = tj.get('settings', {})
for p in settings.get('color', {}).get('palette', []):
    if p.get('slug') in ('text', 'link', 'border'):
        fail('theme.json', f'palette slug "{p["slug"]}" collides with a core class')
for f in settings.get('typography', {}).get('fontSizes', []):
    if f.get('slug') == 'custom':
        fail('theme.json', 'font size slug "custom" collides with a core class')
declared_parts = {p.get('name') for p in tj.get('templateParts', [])}

# functions.php
functions = (theme / 'functions.php').read_text() if (theme / 'functions.php').exists() else ''
if 'get_stylesheet_uri' not in functions and "'style.css'" not in functions:
    warn('functions.php', 'style.css is not enqueued (block themes do not load it by themselves)')

if (theme / 'templates' / 'front-page.html').exists():
    fail('templates/front-page.html', 'the home page should be a page (Settings → Reading), editable like any other')

for part in sorted((theme / 'parts').glob('*.html')):
    if part.stem not in declared_parts:
        fail(f'parts/{part.name}', 'not declared in theme.json templateParts (its area, header/footer, is lost)')

BLOCK_COMMENT = re.compile(r'<!--(.*?)-->', re.S)
GROUP = re.compile(r'<!-- wp:group(\s+(\{.*?\}))?\s*/?-->', re.S)
POST_CONTENT = re.compile(r'<!-- wp:post-content(\s+(\{.*?\}))?\s*/?-->', re.S)
HEX = re.compile(r'#[0-9a-fA-F]{3,8}\b')
SUBPIXEL = re.compile(r'\b\d+\.\d+px\b')


def markup_files():
    for sub in ('templates', 'parts'):
        yield from sorted((theme / sub).glob('*.html'))
    yield from sorted((theme / 'patterns').glob('*.php'))
    if content_dir:
        yield from sorted(content_dir.glob('*.html'))


for path in markup_files():
    rel = path.relative_to(theme) if theme in path.parents else path
    src = path.read_text()
    markup = re.sub(r'<\?php.*?\?>', 'X', src, flags=re.S)

    for m in BLOCK_COMMENT.finditer(markup):
        body = m.group(1).strip()
        if not re.match(r'/?wp:', body):
            fail(rel, f'HTML comment in block markup ("{body[:40]}"): the editor marks the block invalid')

    for m in GROUP.finditer(markup):
        try:
            attrs = json.loads(m.group(2)) if m.group(2) else {}
        except json.JSONDecodeError:
            fail(rel, f'group attributes are not valid JSON: {m.group(2)[:60]}')
            continue
        if not attrs.get('metadata', {}).get('name'):
            fail(rel, 'a group has no name (metadata.name): List View shows only "Group"')

    for m in POST_CONTENT.finditer(markup):
        attrs = json.loads(m.group(2)) if m.group(2) else {}
        if attrs.get('layout', {}).get('type') != 'constrained':
            fail(rel, 'post content without a constrained layout: wide and full alignments stop working')

    for m in HEX.finditer(re.sub(r'<!--.*?-->', '', markup, flags=re.S)):
        warn(rel, f'literal colour {m.group(0)} in markup: use a palette colour so style variations apply')
    for m in re.finditer(r'"(?:background|text)":"(#[0-9a-fA-F]{3,8})"', markup):
        warn(rel, f'literal colour {m.group(1)} in block attributes: use a palette colour')
    for m in SUBPIXEL.finditer(markup):
        warn(rel, f'fraction of a pixel ({m.group(0)})')

    if path.parent.name == 'templates' and '"tagName":"main"' not in markup and 'wp:pattern' not in markup:
        fail(rel, 'no group with tagName "main": the page has no main landmark (and no skip link)')

    if path.parent.name == 'patterns':
        for header in ('Title', 'Slug', 'Categories'):
            if not re.search(rf'^\s*\*\s*{header}:', src, re.M):
                fail(rel, f'pattern header has no {header}')
        m = re.search(r'^\s*\*\s*Slug:\s*(\S+)', src, re.M)
        if m and not m.group(1).startswith(text_domain + '/'):
            warn(rel, f'pattern slug {m.group(1)} does not start with {text_domain}/')
        for m in re.finditer(r"(?:__|_e|esc_html_e|esc_html__|esc_attr_e|esc_attr__)\(\s*'[^']*'\s*,\s*'([^']+)'", src):
            if m.group(1) != text_domain:
                warn(rel, f'text domain "{m.group(1)}" (theme uses "{text_domain}")')

for css in [theme / 'style.css', *sorted((theme / 'assets' / 'css').rglob('*.css'))]:
    if not css.exists():
        continue
    rel = css.relative_to(theme)
    body = css.read_text()
    outside_motion = re.sub(r'@media\s*\(prefers-reduced-motion[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}', '', body)
    if '!important' in outside_motion:
        warn(rel, '!important outside the reduced-motion rule: something upstream wants fixing')
    for m in SUBPIXEL.finditer(body):
        warn(rel, f'fraction of a pixel ({m.group(0)})')

for line in fails + warns:
    print(line)
print(f'{len(fails)} failures, {len(warns)} warnings')
print('CHECK_FAIL' if fails else 'CHECK_OK')
sys.exit(1 if fails else 0)
