# ADR 0001: Context Katmanlaması

## Durum

Kabul edildi

## Karar

Uygulama bounded context yapısıyla ve açık `Presentation`, `Application`, `Domain`, `Infrastructure` katmanlarıyla organize edilir.

## Sonuçlar

Controller sınıfları ince kalır, iş kararları test edilebilir olur ve infrastructure değişiklikleri domain politikasını yeniden yazmayı gerektirmez.
