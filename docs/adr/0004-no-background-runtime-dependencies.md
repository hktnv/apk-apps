# ADR 0004: Arka Plan Runtime Bağımlılığı Yok

## Durum

Kabul edildi

## Karar

Servis Redis, queue, scheduler job, mail veya gizli observer bağımlılığı taşımaz.

## Sonuçlar

Küçük ve özel bir release servisi için operasyon sade kalır. Tüm release işlemleri açık request/response akışlarıdır.
