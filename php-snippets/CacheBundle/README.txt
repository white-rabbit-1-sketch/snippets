Кастомное кеширование уровня запрос-ответ.
После обработки напильником, можно использовать на любом методе (например в сервисах) через АОП.

Пример использования кеширования:
@Cache(ttl=600, tags={"'site-'~requestDto.getSite().getSiteId()~'-agents'"})

Пример инвалидации кеша:
@InvalidateCache(tags={"'site-'~requestDto.getSiteId()~'-agents'", "'site-'~requestDto.getSiteId()~'-agents-'~requestDto.getAgentId()"})