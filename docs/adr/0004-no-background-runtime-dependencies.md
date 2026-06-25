# ADR 0004: No Background Runtime Dependencies

## Status

Accepted

## Decision

The service does not depend on Redis, queues, scheduler jobs, mail, or hidden observers.

## Consequences

Operations remain simple for a small private release service. All release actions are explicit request/response flows.
