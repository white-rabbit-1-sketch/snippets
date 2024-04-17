package crawler

import (
	"context"
	"crawler/crawler/collector"
	"crawler/crawler/collector/request"
	"crawler/crawler/collector/request/filter"
	requestProcessor "crawler/crawler/collector/request/processor"
	responseProcessor "crawler/crawler/collector/response/processor"
	"crawler/crawler/snapshot"
	"os"
	"os/signal"
	"sync"
	"syscall"
	"time"
)

type Crawler struct {
	ctx               context.Context
	wg                sync.WaitGroup
	cancelFunc        context.CancelFunc
	requestDispatcher *RequestDispatcher
	consoleDumper     *ConsoleDumper
	snapshotDumper    *snapshot.SnapshotDumper
}

func NewCrawler(
	ctx context.Context,
	cancelFunc context.CancelFunc,
	parallelRequestsCount int,
	requestTimeout time.Duration,
	filterList []filter.FilterInterface,
	requestProcessorList []requestProcessor.ProcessorInterface,
	responseProcessorList []responseProcessor.ProcessorInterface,
	consoleDumberBufferSize int,
	snapshotFilePath string,
) *Crawler {
	c := &Crawler{
		ctx:            ctx,
		cancelFunc:     cancelFunc,
		snapshotDumper: snapshot.NewSnapshotDumper(snapshotFilePath),
	}

	responseProcessorList = append(responseProcessorList, responseProcessor.NewUrlProcessor(c.onCollectorMeetUrlAddress))

	c.requestDispatcher = NewRequestDispatcher(
		ctx,
		collector.NewCollector(
			filterList,
			requestProcessorList,
			responseProcessorList,
			requestTimeout,
		),
		parallelRequestsCount,
		c.onRequestDispatched,
	)
	c.consoleDumper = NewConsoleDumper(ctx, c.requestDispatcher, consoleDumberBufferSize)

	c.handleShutdown()
	c.handleCompleted()
	c.requestDispatcher.Start()
	c.consoleDumper.Start()

	return c
}

func (c *Crawler) Crawl(urlAddress string) {
	c.requestDispatcher.DispatchRequest(urlAddress)
}

func (c *Crawler) onCollectorMeetUrlAddress(urlAddress string) {
	c.requestDispatcher.DispatchRequest(urlAddress)
}

func (c *Crawler) onRequestDispatched(req *request.Request) {
	c.consoleDumper.DumpRequest(req)
}

func (c *Crawler) Wait() {
	c.requestDispatcher.Wait()
	c.consoleDumper.Wait()
	c.wg.Wait()
}

func (c *Crawler) LoadSnapshot() {
	s := c.snapshotDumper.LoadSnapshot()
	c.requestDispatcher.SetRequestList(s.GetRequestList())
	c.snapshotDumper.RemoveSnapshot()
	c.requestDispatcher.RedispatchRequests()
}

func (c *Crawler) SaveSnapshot() {
	if c.requestDispatcher.HasReadyRequests() {
		s := snapshot.NewSnapshot()
		s.SetRequestList(c.requestDispatcher.GetRequestList())
		c.snapshotDumper.SaveSnapshot(s)
	}
}

func (c *Crawler) handleShutdown() {
	sigCh := make(chan os.Signal, 1)
	signal.Notify(sigCh, os.Interrupt, syscall.SIGTERM, syscall.SIGKILL)

	c.wg.Add(1)
	go func() {
		defer c.wg.Done()

		for {
			select {
			case <-c.ctx.Done():
				c.requestDispatcher.Wait()
				c.SaveSnapshot()

				return
			case <-sigCh:
				c.cancelFunc()
			}
		}
	}()
}

func (c *Crawler) handleCompleted() {
	go func() {
		for {
			time.Sleep(time.Second * 3)
			if !c.requestDispatcher.HasReadyRequests() {
				c.snapshotDumper.RemoveSnapshot()
				c.cancelFunc()
			}
		}
	}()
}
