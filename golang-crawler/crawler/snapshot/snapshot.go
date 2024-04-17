package snapshot

import "crawler/crawler/collector/request"

type Snapshot struct {
	RequestList map[string]*request.Request
}

func NewSnapshot() *Snapshot {
	return &Snapshot{}
}

func (s *Snapshot) SetRequestList(requestList map[string]*request.Request) {
	s.RequestList = requestList
}

func (s *Snapshot) GetRequestList() map[string]*request.Request {
	return s.RequestList
}
