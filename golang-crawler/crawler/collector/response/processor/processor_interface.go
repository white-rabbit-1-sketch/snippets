package processor

import (
	"crawler/crawler/collector/request"
	"net/http"
)

type ProcessorInterface interface {
	Process(req *request.Request, resp *http.Response)
}
