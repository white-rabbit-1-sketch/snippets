��������� ����������� ������ ������-�����.
����� ��������� �����������, ����� ������������ �� ����� ������ (�������� � ��������) ����� ���.

������ ������������� �����������:
@Cache(ttl=600, tags={"'site-'~requestDto.getSite().getSiteId()~'-agents'"})

������ ����������� ����:
@InvalidateCache(tags={"'site-'~requestDto.getSiteId()~'-agents'", "'site-'~requestDto.getSiteId()~'-agents-'~requestDto.getAgentId()"})