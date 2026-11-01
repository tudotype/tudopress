#!/usr/bin/env python3
"""Token tracker: what a site-building flow costs, from Claude Code's own transcripts.

  token-tracker.py start  <folder> "label"     # begin tracking (writes <folder>/marks.json)
  token-tracker.py mark   <folder> "label"     # close the current phase, open the next
  token-tracker.py report <folder> [--session FILE] [--price in,out,cache_write,cache_read]

Reads the session transcript (default: the newest .jsonl of this project's folder in
~/.claude/projects) plus its sub-agent transcripts, counts every API message once, and
sums tokens per phase and per model. Prices are per million tokens and are NOT built in:
pass --price (or put "price" in marks.json) with the figures from Anthropic's pricing page.
"""
import glob, json, os, sys, datetime

PROJECTS = os.path.expanduser("~/.claude/projects")


def now():
    return datetime.datetime.now(datetime.timezone.utc).strftime("%Y-%m-%dT%H:%M:%S.%fZ")


def load(folder):
    p = os.path.join(folder, "marks.json")
    return json.load(open(p)) if os.path.exists(p) else {"marks": []}


def save(folder, data):
    os.makedirs(folder, exist_ok=True)
    json.dump(data, open(os.path.join(folder, "marks.json"), "w"), indent=2, ensure_ascii=False)


def newest_session():
    files = [f for f in glob.glob(PROJECTS + "/*/*.jsonl")]
    return max(files, key=os.path.getmtime) if files else None


def messages(session):
    """Every assistant API message once (streaming repeats a message id; keep the last)."""
    files = [session] + glob.glob(session[:-6] + "/**/*.jsonl", recursive=True)
    seen = {}
    for f in files:
        for line in open(f, errors="replace"):
            try:
                d = json.loads(line)
            except ValueError:
                continue
            m = d.get("message")
            if isinstance(m, dict) and m.get("usage") and m.get("id"):
                seen[m["id"]] = (d.get("timestamp", ""), m.get("model", "?"), m["usage"])
    return sorted(seen.values())


def main():
    if len(sys.argv) < 3 or sys.argv[1] not in ("start", "mark", "report"):
        sys.exit(__doc__)
    cmd, folder = sys.argv[1], os.path.expanduser(sys.argv[2])
    args = sys.argv[3:]
    data = load(folder)
    if cmd in ("start", "mark"):
        label = args[0] if args else cmd
        data["marks"].append({"label": label, "ts": now()})
        save(folder, data)
        print(f"{cmd}: '{label}' at {data['marks'][-1]['ts']}")
        return
    session = args[args.index("--session") + 1] if "--session" in args else newest_session()
    price = None
    if "--price" in args:
        price = [float(x) for x in args[args.index("--price") + 1].split(",")]
    elif data.get("price"):
        price = data["price"]
    marks = data["marks"]
    if not marks:
        sys.exit("No marks yet: run `start` first.")
    bounds = [m["ts"] for m in marks] + ["9999"]
    names = [m["label"] for m in marks]
    rows = [dict(i=0, o=0, cw=0, cr=0, n=0) for _ in marks]
    models = {}
    for ts, model, u in messages(session):
        for k in range(len(marks) - 1, -1, -1):
            if ts >= bounds[k]:
                r = rows[k]
                r["i"] += u.get("input_tokens", 0)
                r["o"] += u.get("output_tokens", 0)
                r["cw"] += u.get("cache_creation_input_tokens", 0)
                r["cr"] += u.get("cache_read_input_tokens", 0)
                r["n"] += 1
                models[model] = models.get(model, 0) + u.get("output_tokens", 0)
                break

    def cost(r):
        return (r["i"] * price[0] + r["o"] * price[1] + r["cw"] * price[2] + r["cr"] * price[3]) / 1e6 if price else None

    print(f"session: {session}")
    print(f"{'phase':<34}{'calls':>6}{'input':>10}{'output':>10}{'cache wr':>11}{'cache rd':>12}" + ("   cost" if price else ""))
    tot = dict(i=0, o=0, cw=0, cr=0, n=0)
    for name, r in zip(names, rows):
        for k in tot:
            tot[k] += r[k]
        c = cost(r)
        print(f"{name[:33]:<34}{r['n']:>6}{r['i']:>10,}{r['o']:>10,}{r['cw']:>11,}{r['cr']:>12,}" + (f"  {c:7.2f}" if c is not None else ""))
    c = cost(tot)
    print(f"{'TOTAL':<34}{tot['n']:>6}{tot['i']:>10,}{tot['o']:>10,}{tot['cw']:>11,}{tot['cr']:>12,}" + (f"  {c:7.2f}" if c is not None else ""))
    print("output tokens by model:", ", ".join(f"{m} {t:,}" for m, t in models.items()))
    if not price:
        print("No prices given: tokens only. Add --price in,out,cache_write,cache_read (USD per million).")


main()
