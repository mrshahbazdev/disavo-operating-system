# ADR-001: Selection of Laravel and PostgreSQL for DOS (Disavo Operating System)

## Status
Accepted

## Context
The Disavo Operating System (DOS) specification mandates an append-only, cyclic, traceable knowledge graph with deep recursive traversal, strict provenance ("Entscheidungspfad"), and institutional memory preservation (no hard deletes, complete auditability).

While initial client documentation made reference to a Next.js/React stack, an evaluation was conducted regarding the core technical risks:
1. **Core Domain is Graph-Centric, Not Simply UI-Centric**: DOS is not a standard CRUD application with an attached graph; it is an organizational knowledge graph with operational workflows bolted on.
2. **Recursive Traversal and Provenance**: Traversal of causal chains (Observation → Insight → Learning → Principle → Decision) requires robust recursive graph traversal with cycle protection (`WITH RECURSIVE` CTEs in PostgreSQL).
3. **Domain Modeling Complexity**: Allocore Learning Framework (ALF), Allocore Module Framework (AMF), and Allocore Review Framework (ARF) require strict domain boundaries, state machines, and multi-tenant isolation.
4. **Audit and Non-Destructive Invalidation**: Every edge and node must have immutable audit trails, append-only invalidation, and strict RBAC.

## Decision
We select **Laravel 12 LTS** (PHP 8.2+) with **PostgreSQL 16** as the core platform for DOS, alongside **Inertia.js + Vue 3** for interactive UI / graph visualization.

### Key Justifications:
1. **PostgreSQL 16 Engine**:
   - High-performance recursive CTE planning for deep cyclic graphs with `NOT e.id = ANY(t.path)` cycle guards.
   - Native `jsonb` with GIN indexing for flexible edge metadata and audit payloads.
   - Direct upgrade path to `pgvector` for Phase 5 semantic search and AI clustering without database migration.
2. **Robust Ecosystem for Domain Monoliths**:
   - `spatie/laravel-model-states`: Explicit state machines for Learning & Principle lifecycles.
   - `spatie/laravel-activitylog`: Complete tamper-proof immutable audit logging.
   - `spatie/laravel-permission`: Multi-tenant RBAC (Owner, Steward, Contributor, Observer).
   - `staudenmeir/laravel-adjacency-list`: Recursive organizational module hierarchies.
   - `pestphp/pest`: First-class architecture tests enforcing domain boundary isolation.
3. **Inertia.js + Vue 3 Frontend**:
   - Preserves component-based modern SPA reactive UI (matching the visual fidelity required by the spec).
   - Seamlessly integrates with graph canvas libraries (Cytoscape.js) and rapid quick-capture dialogs.
   - Eliminates client-server state drift and duplicate validation logic.

## Consequences
- **Positive**: High development velocity, rock-solid data integrity, built-in security, out-of-the-box queue and cache management, zero graph traversal penalty.
- **Compliance / Handover**: Documented formally so client stakeholders have clear justification for the backend stack divergence from initial Next.js references.
