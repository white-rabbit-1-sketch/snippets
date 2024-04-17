package processor

import (
	"crawler/crawler/collector/request"
)

type ProcessorInterface interface {
	Process(req *request.Request)
}
