# DOS — Disavo Operating System

> **"A cyclic, traceable, append-only knowledge graph with CRUD bolted on."**

DOS is an organizational operating system designed to preserve, connect, and evolve strategic organizational intelligence across the lifetime of a business.

---

## Core Architecture

1. **ALF (Allocore Learning Framework)**:
   - Complete cognitive traceability: `Observation` → `Insight` → `Learning` → `Principle` → `Decision`.
   - Strict state machines for learning validation and non-destructive principle supersession.
2. **AMF (Allocore Module Framework)**:
   - Hierarchical organizational adjacency tree structure.
   - Maturity delta scoring based on database-weighted audit questions against target states (*Zielzustand*).
   - Time-series KPI readings and operational toolkits.
3. **ARF (Allocore Review Framework)**:
   - Strategic review loops that systematically spawn draft learnings (`Review -(Generates)-> Learning`).
4. **Knowledge Graph Engine**:
   - Append-only `knowledge_edges` table with non-destructive invalidation.
   - Recursive CTE provenance queries and cycle safety guards.
   - Multi-hop Cytoscape.js neighborhood queries (`GET /api/v1/graph/{type}/{id}?depth=3`).
   - Active tension and contradiction detection (`GET /api/v1/graph/contradictions`).
   - Orphan knowledge node reporting (`GET /api/v1/graph/orphans` and `php artisan dos:graph:orphans`).
   - Foreign-key backfill engine (`php artisan dos:graph:backfill`).

---

## Shared Hosting & Deployment
- Includes root `.htaccess` for standard cPanel / shared hosting environments directing web requests to `/public/`.
- Frontend assets pre-built and compiled via Vite (`public/build`).

---

## Getting Started

```bash
# 1. Install dependencies
composer install --optimize-autoloader
npm install && npm run build

# 2. Environment configuration
cp .env.example .env
php artisan key:generate

# 3. Database migrations & Seed Pack
php artisan migrate --seed

# 4. Verify test suite
php artisan test
```
