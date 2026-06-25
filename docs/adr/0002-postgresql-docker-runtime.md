# ADR 0002: PostgreSQL ve Docker Çalışma Zamanı

## Durum

Kabul edildi

## Karar

Desteklenen tek veritabanı PostgreSQL 17'dir. Üretim çalışma zamanı Docker Compose üzerinden Nginx, PHP-FPM ve PostgreSQL kullanır.

## Sonuçlar

Yerel ve üretim veritabanı davranışı tutarlı kalır. Sürüm metadata bilgisi açısından önemli özellik testlerinde uygulama SQLite'a özel davranışlardan kaçınır.
