package filter

import (
	"crawler/crawler/collector/request"
	"net/http"
	"strings"
	"time"
)

type MimeTypeFilter struct {
	httpClient        *http.Client
	whiteMimeTypeList []string
}

func NewMimeTypeFilter(whiteMimeTypeList []string, requestTimeout time.Duration) *MimeTypeFilter {
	return &MimeTypeFilter{
		httpClient:        &http.Client{Timeout: requestTimeout},
		whiteMimeTypeList: whiteMimeTypeList,
	}
}

func (f *MimeTypeFilter) Filter(req *request.Request) {
	response, err := f.httpClient.Head(req.GetUrl())
	if err != nil {
		req.MarkAsError(err.Error())
	} else {
		isMimeTypeMatch := false
		contentType := response.Header.Get("Content-Type")
		for _, mimeType := range f.whiteMimeTypeList {
			if strings.Contains(contentType, mimeType) {
				isMimeTypeMatch = true

				break
			}
		}
		response.Body.Close()

		if !isMimeTypeMatch {
			req.MarkAsFiltered("Mime type mismatch")
		}
	}
}
