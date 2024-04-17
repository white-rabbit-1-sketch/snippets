package processor

import (
	"crawler/crawler/collector/request"
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

func (p *ProcessorManager) Process(req *request.Request) {
	for _, prcsr := range p.processorList {
		if !req.IsReady() {
			break
		}

		prcsr.Process(req)
	}
}
