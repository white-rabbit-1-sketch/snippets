package filter

import (
	"crawler/crawler/collector/request"
	"net/url"
)

type DomainFilter struct {
	whiteDomain string
}

func NewDomainFilter(address string) *DomainFilter {
	u, err := url.Parse(address)
	if err != nil {
		panic(err)
	}

	return &DomainFilter{
		whiteDomain: u.Hostname(),
	}
}

func (f *DomainFilter) Filter(req *request.Request) {
	u, err := url.Parse(req.GetUrl())
	if err != nil {
		req.MarkAsError(err.Error())
	} else if u.Hostname() != f.whiteDomain {
		req.MarkAsFiltered("Domain mismatch")
	}
}
