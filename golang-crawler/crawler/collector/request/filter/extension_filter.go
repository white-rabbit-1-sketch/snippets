package filter

import (
	"crawler/crawler/collector/request"
	"path/filepath"
)

type ExtensionFilter struct {
	whiteExtensionList []string
}

func NewExtensionFilter(whiteExtensionList []string) *ExtensionFilter {
	whiteExtensionList = append(whiteExtensionList, "") // make sure empty extension is here

	return &ExtensionFilter{
		whiteExtensionList: whiteExtensionList,
	}
}

func (f *ExtensionFilter) Filter(req *request.Request) {
	if string(req.GetUrl()[len(req.GetUrl())-1]) != "/" {
		extension := filepath.Ext(filepath.Base(req.GetUrl()))

		isExtensionMatch := false
		for _, whiteExtension := range f.whiteExtensionList {
			if extension == whiteExtension {
				isExtensionMatch = true

				break
			}
		}

		if !isExtensionMatch {
			req.MarkAsFiltered("Extension mismatch")
		}
	}
}
