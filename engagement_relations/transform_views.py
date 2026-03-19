import re
from pathlib import Path

base = Path('.').resolve()
exclude = {'layout.php', 'login.php', 'logout.php', 'schema_check.php', 'update_audit_roles.php'}
for p in sorted(base.glob('*.php')):
    if p.name in exclude:
        continue
    text = p.read_text(encoding='utf-8')
    if '<!DOCTYPE' not in text and '<html' not in text:
        print('skip no html', p.name)
        continue

    m_doctype = re.search(r'<!DOCTYPE[^>]*>', text, re.I)
    if not m_doctype:
        print('no doctype', p.name)
        continue

    pre = text[:m_doctype.start()]
    pre = re.sub(r"\bob_start\(\);\s*", '', pre)

    m_title = re.search(r'<title>(.*?)</title>', text, re.I | re.S)
    title = m_title.group(1).strip() if m_title else p.stem.replace('-', ' ').title()

    head_match = re.search(r'<head.*?>(.*?)</head>', text, re.I | re.S)
    extraCss = []
    if head_match:
        head = head_match.group(1)
        extraCss = re.findall(r'<link[^>]+href=["\']([^"\']+)["\'][^>]*>', head, re.I)

    main_match = re.search(r'<main[^>]*>(.*?)</main>', text, re.I | re.S)
    main_content = main_match.group(1).strip() if main_match else ''

    script_paths = re.findall(r'<script[^>]+src=["\']([^"\']+)["\'][^>]*></script>', text, re.I)

    activePage = p.stem

    out_parts = []
    out_parts.append('<?php')
    if pre.strip():
        out_parts.append(pre.strip())
    out_parts.append(f"$pageTitle = '{title}';")
    out_parts.append(f"$activePage = '{activePage}';")
    out_parts.append('$extraCss = ' + str(list(dict.fromkeys(extraCss))) + ';')
    out_parts.append('$extraJs = ' + str(list(dict.fromkeys(script_paths))) + ';')
    out_parts.append('ob_start();')
    out_parts.append('?>')

    out_parts.append(main_content)

    out_parts.append('<?php')
    out_parts.append('$moduleContent = ob_get_clean();')
    out_parts.append("include __DIR__ . '/layout.php';")

    p.write_text('\n'.join(out_parts), encoding='utf-8')
    print('updated', p.name)
