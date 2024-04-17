package crawler

import (
	"context"
	"crawler/crawler/collector"
	"crawler/crawler/collector/request"
	"crawler/workerpool"
	"sync"
)

type RequestDispatcher struct {
	ctx                 context.Context
	wg                  sync.WaitGroup
	workerPool          *workerpool.WorkerPool
	clctr               *collector.Collector
	requestQueue        chan string
	requestResultQueue  chan *request.Request
	onRequestDispatched func(req *request.Request)
	requestList         map[string]*request.Request
}

func NewRequestDispatcher(
	ctx context.Context,
	clctr *collector.Collector,
	parallelRequestsCount int,
	onRequestDispatched func(req *request.Request),
) *RequestDispatcher {
	m := &RequestDispatcher{
		ctx:                 ctx,
		workerPool:          workerpool.NewWorkerPool(ctx, parallelRequestsCount, parallelRequestsCount*10),
		clctr:               clctr,
		requestQueue:        make(chan string, parallelRequestsCount*10),
		requestResultQueue:  make(chan *request.Request, parallelRequestsCount*10),
		onRequestDispatched: onRequestDispatched,
		requestList:         make(map[string]*request.Request),
	}

	return m
}

func (d *RequestDispatcher) Start() {
	d.workerPool.Start()

	d.wg.Add(1)
	go func() {
		defer d.wg.Done()

		for {
			select {
			case urlAddress := <-d.requestQueue:
				d.handleRequest(urlAddress, false)
			case <-d.ctx.Done():
				d.workerPool.Wait()
				if len(d.requestQueue) == 0 {
					return
				}
			}
		}
	}()

	d.wg.Add(1)
	go func() {
		defer d.wg.Done()

		for {
			select {
			case req := <-d.requestResultQueue:
				d.handleRequestResult(req)
			case <-d.ctx.Done():
				d.workerPool.Wait()
				if len(d.requestResultQueue) == 0 {
					return
				}
			}
		}
	}()
}

func (d *RequestDispatcher) Wait() {
	d.workerPool.Wait()
	d.wg.Wait()
}

func (d *RequestDispatcher) DispatchRequest(urlAddress string) {
	go func() {
		d.requestQueue <- urlAddress
	}()
}

func (d *RequestDispatcher) DispatchRequestResult(req *request.Request) {
	go func() {
		d.requestResultQueue <- req
	}()
}

func (d *RequestDispatcher) handleRequest(urlAddress string, force bool) {
	req := d.requestList[urlAddress]
	if force || req == nil {
		req = request.NewRequest(urlAddress)
		d.requestList[urlAddress] = req
		d.workerPool.AddTask(workerpool.Task{
			HandlerFunc: func() {
				d.clctr.Visit(req)
				d.DispatchRequestResult(req)
			},
			CancelFunc: func() {
				req.MarkAsCanceled()
				d.DispatchRequestResult(req)
			},
		})
	}
}

func (d *RequestDispatcher) handleRequestResult(req *request.Request) {
	d.onRequestDispatched(req)
}

func (d *RequestDispatcher) HasReadyRequests() bool {
	result := false

	for _, req := range d.requestList {
		if req.IsReady() {
			result = true

			break
		}
	}

	return result
}

func (d *RequestDispatcher) RedispatchRequests() {
	for _, req := range d.requestList {
		if req.IsReady() || req.IsCanceled() {
			d.handleRequest(req.GetUrl(), true)
		}
	}
}

func (d *RequestDispatcher) GetRequestList() map[string]*request.Request {
	return d.requestList
}

func (d *RequestDispatcher) SetRequestList(requestList map[string]*request.Request) {
	d.requestList = requestList
}
