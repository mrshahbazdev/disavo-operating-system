# DOS — Disavo Operating System

> **"Die eigentliche Macht von DOS entsteht nicht durch die Objekte. Sie entsteht durch deren Beziehungen."**
> 
> *DOS is not a CRUD application with a graph bolted on. It is a **cyclic, traceable, append-only knowledge graph with CRUD bolted on**.*

---

## Table of Contents

1. [Architectural Principles](#1-architectural-principles)
2. [Domain Frameworks (The 3 Pillars)](#2-domain-frameworks-the-3-pillars)
   - [ALF — Allocore Learning Framework](#21-alf--allocore-learning-framework)
   - [AMF — Allocore Module Framework](#22-amf--allocore-module-framework)
   - [ARF — Allocore Review Framework](#23-arf--allocore-review-framework)
3. [Knowledge Graph Engine](#3-knowledge-graph-engine)
   - [Graph Schema & Edge Relations](#31-graph-schema--edge-relations)
   - [Recursive Traversal & Cycle Guard](#32-recursive-traversal--cycle-guard)
   - [Cytoscape.js Multi-Hop Neighborhood API](#33-cytoscapejs-multi-hop-neighborhood-api)
   - [Contradiction & Tension Detection Engine](#34-contradiction--tension-detection-engine)
   - [Orphan Knowledge Node Detection](#35-orphan-knowledge-node-detection)
   - [Relational Edge Backfill Engine](#36-relational-edge-backfill-engine)
4. [Tenancy & Security Model](#4-tenancy--security-model)
5. [Complete REST API Reference](#5-complete-rest-api-reference)
6. [Artisan CLI Commands](#6-artisan-cli-commands)
7. [Deployment & Shared Hosting Guide](#7-deployment--shared-hosting-guide)
8. [Automated Testing & Verification](#8-automated-testing--verification)

---

## 1. Architectural Principles

Every technical decision in DOS follows from three non-negotiables:

| Requirement | Technical Consequence |
|---|---|
| **Every decision traceable to its origin** | Append-only edge table (`knowledge_edges`) + recursive CTE traversal with cycle prevention. |
| **Knowledge must never be lost** | No hard deletes (`forceDelete` throws `LogicException`). Non-destructive versioning (`Supersedes` edges). |
| **The loop never ends** | Reviews generate Learnings that generate Principles that govern Modules — a continuous cyclic graph. |

---

## 2. Domain Frameworks (The 3 Pillars)

```
       ┌──────────────────────────────────────────────────────────┐
       │                                                          │
       ▼                                                          │
┌──────────────┐     ┌──────────────┐     ┌──────────────┐        │
│     AMF      │────▶│     ARF      │────▶│     ALF      │────────┘
│ (Modules &   │     │ (Strategic   │     │ (Knowledge   │
│  Audits)     │     │  Reviews)    │     │  Synthesis)  │
└──────────────┘     └──────────────┘     └──────────────┘
```

### 2.1 ALF — Allocore Learning Framework

ALF captures and refines organizational intelligence through a verifiable cognitive trail:

$$\text{Observation} \xrightarrow{\text{produces}} \text{Insight} \xrightarrow{\text{validates}} \text{Learning} \xrightarrow{\text{promotes}} \text{Principle} \xleftarrow{\text{justified\_by}} \text{Decision}$$

- **Observation**: Raw contextual data and operational observations (`content`, `context`).
- **Insight**: Pattern recognition and synthesis (`title`, `implications`).
- **Learning**: Actionable organizational realization, managed through strict state machines:
  - `Draft` $\rightarrow$ `UnderValidation` $\rightarrow$ `Validated` $\rightarrow$ `Promoted` (or `Rejected` / `Deprecated`).
- **Principle**: Canonical organizational law governing operations:
  - States: `Proposed` $\rightarrow$ `Active` $\rightarrow$ `Superseded` / `Retired`.
  - Non-destructive versioning via `SupersedePrinciple` action: creates an updated `version + 1` record, sets state of previous version to `Superseded`, and links them via a `Supersedes` edge.
- **Question**: Open strategic inquiry (*Offene Fragen*), answerable by Learnings.
- **Decision**: High-stakes choice justified explicitly by a governing Principle (`justified_by` edge).

### 2.2 AMF — Allocore Module Framework

AMF models the organizational development structure:

- **6 Canonical Modules**:
  1. `Unternehmensentwicklung` (Corporate Development)
  2. `Markenaufbau` (Brand Building)
  3. `Nachfolge` (Succession & Generational Transfer)
  4. `Unternehmerentwicklung` (Founder & Leadership Development)
  5. `Beteiligungsmanagement` (Portfolio Management)
  6. `Kapitalallokation` (Capital Allocation)
- **Hierarchical Adjacency Tree**: Implemented with `staudenmeir/laravel-adjacency-list` for infinite recursive depth (`parent_id`, ancestors, descendants).
- **Goal (*Zielzustand*)**: Versioned target maturity state and score for each module.
- **Audit Engine**:
  - Reusable `AuditTemplate` with database-weighted `AuditQuestion` items (weights are configured in DB, never hardcoded).
  - Executed via `AuditRun` with question-level `AuditResponse`.
  - **Maturity Scoring Service**:
    $$\text{Score} = \frac{\sum (\text{actual} / \text{max} \times \text{weight})}{\sum \text{weight}} \times 100$$
    Calculates the exact maturity delta between the current audit score and the module's target *Zielzustand*.
  - Finalizing an audit automatically records a `Measures` graph edge from `AuditRun` to `Module`.
- **KPI Time Series**: Metric definitions (`Kpi`) with time-series `KpiReading` entries indexed by `[kpi_id, recorded_at]`.
- **Operational Tools**: Typed toolkits (`checklist`, `template`, `whitepaper`, `saas`, `ai_function`) connected via `Improves` graph edges.

### 2.3 ARF — Allocore Review Framework

ARF drives continuous organizational learning and closes the loop:

- **Review**: Scheduled cadence (`period`, `review_date`, `summary`, `status`).
- **ReviewItem**: Module-specific findings, evaluations, and progress trackers.
- **Improvement**: Corrective and evolutionary action items.
- **Loop Closure Action (`CloseReview`)**:
  - Closing a review executes `SpawnLearningFromReview`.
  - Automatically synthesizes findings into a new `Draft` Learning in ALF.
  - Links `Review -(generates)-> Learning`.
  - The Learning is then validated by a Steward and promoted to an active Principle that `governs` a Module, closing the cycle.

---

## 3. Knowledge Graph Engine

### 3.1 Graph Schema & Edge Relations

All connections live in the append-only `knowledge_edges` table:
- `id` (bigint PK)
- `tenant_id` (foreign key, strict isolation)
- `source_type` / `source_id` (polymorphic enum + ID)
- `target_type` / `target_id` (polymorphic enum + ID)
- `relation` (`RelationType` enum)
- `strength` (0–100 integer confidence/weight)
- `meta` (jsonb / json context)
- `invalidated_at` / `invalidated_by` / `invalidation_reason` (append-only tombstoning)
- `created_by` / `created_at` / `updated_at`

#### Registered Edge Relations:

| Relation | Source Node(s) | Target Node(s) | Semantic Meaning |
|---|---|---|---|
| `produces` | `Observation` | `Insight` | Observation provides evidentiary basis for Insight |
| `validates` | `Insight` | `Learning` | Empirical insight validates organizational learning |
| `promotes` | `Learning` | `Principle` | Validated learning is elevated to a governing Principle |
| `answers` | `Learning` | `Question` | Learning resolves an open strategic question |
| `raises` | Any Node | `Question` | Any entity can raise a new open question |
| `governs` | `Principle` | `Module` | Principle establishes law/policy over a Module |
| `measures` | `Audit` | `Module` | Executed audit measures performance of Module |
| `quantifies` | `Kpi` | `Module` | Metric quantifies operational state of Module |
| `improves` | `Tool` | `Module` | Tool optimizes execution within Module |
| `evaluates` | `Review` | `Module`, `Principle` | Review assesses module performance or principle health |
| `generates` | `Review` | `Learning` | Review loop closure generates a new Draft Learning |
| `supersedes` | `Principle` | `Principle` | New principle version replaces older principle |
| `contradicts` | `Learning` | `Principle` | Learning disproves or tensions an active Principle |
| `justified_by` | `Decision` | `Principle` | Decision traces directly to governing Principle |

### 3.2 Recursive Traversal & Cycle Guard

- **Provenance Path (`ProvenancePathQuery`)**:
  - Traverses backward from any target node (e.g. `Decision` or `Principle`) through reverse relations (`justified_by`, `promotes`, `validates`, `produces`) back to the initial `Observation`.
  - Employs recursive CTE with an array-based cycle detector to guarantee termination even in cyclic organizational graphs.
- **Impact Radius (`ImpactRadiusQuery`)**:
  - Traverses forward from any node up to depth $N$, revealing all downstream modules, principles, and tools impacted by a change.

### 3.3 Cytoscape.js Multi-Hop Neighborhood API

- **Endpoint**: `GET /api/v1/graph/{type}/{id}?depth=3`
- Returns a standardized Cytoscape.js graph payload ready for direct frontend rendering:
```json
{
  "elements": {
    "nodes": [
      {
        "data": {
          "id": "observation:1",
          "label": "Founder Operational Drag",
          "type": "observation",
          "type_label": "Observation",
          "model_id": 1,
          "summary": "Founders spending 40% time on operational fires.",
          "is_center": true,
          "state": null
        }
      }
    ],
    "edges": [
      {
        "data": {
          "id": "edge_1",
          "source": "observation:1",
          "target": "insight:1",
          "label": "produces",
          "relation": "produces",
          "strength": 100,
          "meta": {},
          "created_at": "2026-09-14T05:45:00Z"
        }
      }
    ]
  },
  "stats": {
    "center": "observation:1",
    "depth": 3,
    "node_count": 5,
    "edge_count": 4
  }
}
```

### 3.4 Contradiction & Tension Detection Engine

DOS actively surfaces tensions rather than allowing contradictory beliefs to silently co-exist:
- **Endpoint**: `GET /api/v1/graph/contradictions`
- **Service**: `ContradictionDetectionService`
- Scans all active `Contradicts` edges (`Learning -> Principle`).
- Tags severity (`critical` if Principle is `Active`, `resolved` if Principle has been superseded/retired).
- Cross-references and returns all `Module` entities governed by the contradicted principle, giving immediate operational clarity.

### 3.5 Orphan Knowledge Node Detection

Prevents knowledge loss and siloed documents:
- **Endpoint**: `GET /api/v1/graph/orphans`
- **Artisan Command**: `php artisan dos:graph:orphans`
- Scans all 13 node types across the database and flags nodes with 0 active incoming and 0 active outgoing edges.
- As soon as a node is linked into the graph, it automatically leaves the orphan pool.

### 3.6 Relational Edge Backfill Engine

- **Artisan Command**: `php artisan dos:graph:backfill {--tenant=}`
- Inspects relational foreign keys across modules, audits, KPIs, tools, reviews, and versioned principles, converting them non-destructively into explicit `knowledge_edges`.
- Completely **idempotent**: running multiple times never duplicates active edges.

---

## 4. Tenancy & Security Model

- **Database-Level Isolation**: Every entity implements `BelongsToTenant`, enforcing tenant scoping via `TenantScope`.
- **Tenant Context (`TenantContext`)**: Resolved per request via `IdentifyTenant` middleware through `X-Tenant` header or domain mapping.
- **Tenant-Scoped Roles (`spatie/laravel-permission`)**:
  - `Owner`: Full organizational control and tenant administration.
  - `Steward`: Quality gatekeeper, responsible for validating Learnings and promoting Principles.
  - `Contributor`: Captures Observations, proposes Learnings, and fills Audits.
  - `Observer`: Read-only access to knowledge graph visualizations.
- **Immutable Audit Trail (`spatie/laravel-activitylog`)**: Every mutation is logged with actor ID, batch UUID, and tenant scope.
- **Strict Prohibition of Hard Deletes**:
  - Base model `DosModel` overrides `forceDelete()` to throw a `LogicException`.
  - Deletions only occur via `SoftDeletes`.
  - Edges are never deleted; they are invalidated with `invalidated_at`, `invalidated_by`, and `invalidation_reason`.

---

## 5. Complete REST API Reference

All routes are prefixed with `/api/v1` and require tenant identification:

### Quick Capture
- `POST /api/v1/capture` — Capture observations, learnings, or questions in <60 seconds.

### ALF (Knowledge Framework)
- `GET /api/v1/observations` — List observations.
- `POST /api/v1/observations` — Create an observation.
- `GET /api/v1/observations/{id}` — Get observation details.
- `GET /api/v1/learnings` — List learnings.
- `POST /api/v1/learnings` — Create learning in `Draft` state.
- `GET /api/v1/learnings/{id}` — Get learning details.
- `POST /api/v1/learnings/{id}/validate` — Validate learning (transitions to `Validated`).
- `POST /api/v1/learnings/{id}/promote` — Promote validated learning to an active `Principle`.
- `GET /api/v1/principles` — List principles.
- `GET /api/v1/principles/{id}` — Get principle details.
- `GET /api/v1/principles/{id}/provenance` — Trace complete origin path back to initial observation.
- `GET /api/v1/principles/{id}/impact` — Trace downstream impact radius on modules and tools.
- `POST /api/v1/principles/{id}/supersede` — Non-destructively supersede principle with a new version.
- `GET /api/v1/questions` — List strategic questions.
- `POST /api/v1/questions` — Post an open question.

### AMF (Organizational Development)
- `GET /api/v1/modules` — List organizational modules and tree hierarchy.
- `GET /api/v1/modules/{id}` — Get module details with children and ancestors.
- `GET /api/v1/modules/{id}/maturity` — Get latest audit score and Zielzustand delta.
- `POST /api/v1/modules/{id}/audits/{templateId}/run` — Initialize an audit run for a module.
- `POST /api/v1/audits/{runId}/score` — Submit audit responses, calculate weighted score, and link `Measures` edge.

### ARF (Review Loops)
- `GET /api/v1/reviews` — List reviews.
- `POST /api/v1/reviews` — Schedule a new review.
- `GET /api/v1/reviews/{id}` — Get review details and findings.
- `POST /api/v1/reviews/{id}/close` — Close review, spawning a `Draft` Learning to close the loop.

### Knowledge Graph Engine
- `GET /api/v1/graph/contradictions` — Surface active tensions between Learnings and Principles.
- `GET /api/v1/graph/orphans` — Report disconnected nodes across the system.
- `GET /api/v1/graph/{type}/{id}?depth=3` — Cytoscape.js multi-hop graph neighborhood.
- `POST /api/v1/edges` — Manually establish a verified edge between two knowledge nodes.
- `DELETE /api/v1/edges/{id}` — Invalidate an edge with a mandatory reason (preserved in history).

---

## 6. Artisan CLI Commands

```bash
# Detect and report isolated knowledge nodes
php artisan dos:graph:orphans
php artisan dos:graph:orphans --tenant=1 -v

# Non-destructively backfill graph edges from historical foreign keys
php artisan dos:graph:backfill
php artisan dos:graph:backfill --tenant=1
```

---

## 7. Deployment & Shared Hosting Guide

DOS is built to be deployed on modern cloud infrastructure (PostgreSQL 16, Redis, Docker) as well as **Standard cPanel / Apache Shared Hosting**:

### 7.1 Shared Hosting Setup (.htaccess)
- The repository includes a **root `.htaccess`** file that automatically routes all incoming requests to the `/public` directory without requiring document root modifications in cPanel.
- Direct access to sensitive files (`.env`, `composer.json`, `artisan`, `.git`) is blocked at the web server level:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(\.env|\.git|composer\.(json|lock)|package\.(json|lock)|artisan) - [F,L,NC]
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```
- Production Vite assets (`manifest.json`, CSS, JS bundles) are pre-compiled and tracked in Git under `public/build/`, allowing seamless deployment even when Node.js is unavailable on the hosting server.

### 7.2 Installation Commands

```bash
# 1. Clone repository
git clone https://github.com/mrshahbazdev/disavo-operating-system.git
cd disavo-operating-system

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Storage permissions (critical on shared hosting)
chmod -R 775 storage bootstrap/cache

# 5. Database setup & initial seed pack
php artisan migrate --force --seed
```

---

## 8. Automated Testing & Verification

The test suite validates architectural integrity, tenancy scoping, graph algorithms, and state transitions across 30 comprehensive tests:

```bash
php artisan test
```

```
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: phpunit.xml

..............................                                    30 / 30 (100%)

Time: 00:03.134, Memory: 50.00 MB

OK (30 tests, 228 assertions)
```

### Test Suite Map:
1. `tests/Feature/Phase3KnowledgeGraphTest.php` — Cytoscape multi-hop BFS, contradiction detection, orphan detection, backfill idempotency.
2. `tests/Feature/ModuleHierarchyTest.php` — Canonical modules, adjacency tree recursion, and governing principles.
3. `tests/Feature/AuditAndMaturityTest.php` — Database-weighted audits, maturity delta scoring against target state (*Zielzustand*).
4. `tests/Feature/ReviewLoopTest.php` — Review closure spawning draft learnings, closing the loop.
5. `tests/Feature/KnowledgeGraphTest.php` — Recursive CTE provenance path queries, cycle safety guards, append-only edge invalidations.
6. `tests/Feature/KnowledgeStateTest.php` — Spatie model state lifecycles and non-destructive principle supersession.
7. `tests/Feature/KpiAndToolTest.php` — Time-series KPI readings and operational tool linking.
8. `tests/Feature/TenancyIsolationTest.php` — Strict multi-tenant query scoping and cross-tenant leakage prevention.
9. `tests/Feature/QuickCaptureTest.php` — Sub-60-second capture endpoint.
10. `tests/Unit/ArchitectureBoundaryTest.php` — Enforces that all 13 domain models implement `KnowledgeNode` and extend `DosModel`.
