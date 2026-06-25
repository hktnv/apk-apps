# ADR 0001: Context Layering

## Status

Accepted

## Decision

The app is organized by bounded context with explicit `Presentation`, `Application`, `Domain`, and `Infrastructure` layers.

## Consequences

Controllers stay thin, business decisions stay testable, and infrastructure can change without rewriting domain policy.
