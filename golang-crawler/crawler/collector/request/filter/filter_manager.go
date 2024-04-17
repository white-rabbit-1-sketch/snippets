package filter

import (
	"crawler/crawler/collector/request"
)

type FilterManager struct {
	filterList []FilterInterface
}

func NewFilterManager(
	filterList []FilterInterface,
) *FilterManager {
	return &FilterManager{
		filterList: filterList,
	}
}

func (f *FilterManager) Filter(req *request.Request) {
	for _, fltr := range f.filterList {
		if !req.IsReady() {
			break
		}

		fltr.Filter(req)
	}
}
