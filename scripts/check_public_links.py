#!/usr/bin/env python3
"""Read-only public URL probe. Browser interaction remains a manual gate.

No CRM credentials, cookies, customer IDs, login, booking or writes.
This checks HTTP status/redirect only, NOT gameplay, gallery privacy or slots.
"""
from __future__ import annotations

import argparse
import urllib.error
import urllib.request
from urllib.parse import urlsplit, urlunsplit

URLS = {
    "jogos_demo": "https://jogos.fotosdenatal.com/",
    "galeria_demo": "https://jogos.fotosdenatal.com/demo/fotos",
    "horarios": "https://horarios.fotosdenatal.com/",
    "instagram": "https://instagram.com/estudioevydenciaa",
    "site_natal": "https://fotosdenatal.com/",
    "como_chegar": "https://ir.fotosdenatal.com/",
    "checkout": "https://reserva.fotosdenatal.com/",
}


def safe_url(url: str) -> str:
    parsed = urlsplit(url)
    return urlunsplit((parsed.scheme, parsed.netloc, parsed.path, "", ""))


def check(name: str, url: str, timeout: int) -> None:
    request = urllib.request.Request(
        url, headers={"User-Agent": "Mozilla/5.0 (Evydencia-ReadOnly-Link-Audit)"}
    )
    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            final_url = safe_url(response.geturl())
            content_type = response.headers.get("Content-Type", "?")
            print(f"{name:15} HTTP {response.status:3} {final_url} ({content_type})")
    except urllib.error.HTTPError as error:
        print(f"{name:15} HTTP {error.code:3} {safe_url(error.geturl())} [BROWSER_CHECK]")
    except (urllib.error.URLError, TimeoutError, OSError) as error:
        print(f"{name:15} UNVERIFIED [{type(error).__name__}] browser check required")


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--timeout", type=int, default=8)
    parser.add_argument("--list-only", action="store_true")
    options = parser.parse_args()
    if options.timeout < 1 or options.timeout > 30:
        parser.error("--timeout must be between 1 and 30")
    print("READ-ONLY LINK PROBE. HTTP 200 does NOT prove that games, gallery or checkout work.")
    for name, url in URLS.items():
        if options.list_only:
            print(f"{name:15} {url}")
        else:
            check(name, url, options.timeout)


if __name__ == "__main__":
    main()
