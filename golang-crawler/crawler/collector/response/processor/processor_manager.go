package processor

import (
	"crawler/crawler/collector/request"
	"net/http"
)

type ProcessorManager struct {
	processorList []ProcessorInterface
}

func NewProcessorManager(
	processorList []ProcessorInterface,
) *ProcessorManager {
	return &ProcessorManager{
		processorList: processorList,
	}
}

func (p *ProcessorManager) Process(req *request.Request, resp *http.Response) {
	for _, prcsr := range p.processorList {
		if !req.IsReady() {
			break
		}

		prcsr.Process(req, resp)
	}
}
