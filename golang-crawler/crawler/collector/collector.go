package collector

import (
	"crawler/crawler/collector/request"
	"crawler/crawler/collector/request/filter"
	requestProcessor "crawler/crawler/collector/request/processor"
	respinseProcessor "crawler/crawler/collector/response/processor"
	"net/http"
	"time"
)

type Collector struct {
	httpClient               *http.Client
	requestFilterManager     *filter.FilterManager
	requestProcessorManager  *requestProcessor.ProcessorManager
	responseProcessorManager *respinseProcessor.ProcessorManager
}

func NewCollector(
	requestFilterList []filter.FilterInterface,
	requestProcessorList []requestProcessor.ProcessorInterface,
	responseProcessorList []respinseProcessor.ProcessorInterface,
	requestTimeout time.Duration,
) *Collector {
	c := &Collector{
		httpClient:               &http.Client{Timeout: requestTimeout},
		requestFilterManager:     filter.NewFilterManager(requestFilterList),
		requestProcessorManager:  requestProcessor.NewProcessorManager(requestProcessorList),
		responseProcessorManager: respinseProcessor.NewProcessorManager(responseProcessorList),
	}

	return c
}

func (c *Collector) Visit(req *request.Request) {
	c.requestFilterManager.Filter(req)
	c.requestProcessorManager.Process(req)

	if req.IsReady() {
		resp, err := http.Get(req.GetUrl())
		if err != nil {
			req.MarkAsError(err.Error())
		} else {
			c.responseProcessorManager.Process(req, resp)
		}
		resp.Body.Close()
	}

	if req.IsReady() {
		req.MarkAsVisited()
	}
}

func (c *Collector) Head(request *request.Request) (http.Header, error) {
	resp, err := http.Head(request.GetUrl())
	if err == nil {
		defer resp.Body.Close()
	}

	return resp.Header, err
}
