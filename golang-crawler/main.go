package main

import (
	"context"
	"crawler/crawler"
	"crawler/crawler/collector/request/filter"
	requestProcessor "crawler/crawler/collector/request/processor"
	responseProcessor "crawler/crawler/collector/response/processor"
	"flag"
	"fmt"
	"os"
	"time"
)

func main() {
	url := flag.String("url", "", "Url to crawl")
	snapshotFilePath := flag.String("snapshot-file-path", "", "Path to save/load state file")
	dataFilePath := flag.String("data-file-path", "", "Path to save visited sites")
	useState := flag.Bool("use-state", false, "If provided - state will be used; otherwise not")
	flag.Parse()

	if *url == "" || *snapshotFilePath == "" || *dataFilePath == "" {
		fmt.Println("You must declare some parameters")
		flag.PrintDefaults()
		os.Exit(1)
	}

	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()

	requestTimeout := time.Second * 5
	c := crawler.NewCrawler(
		ctx,
		cancel,
		1,
		requestTimeout,
		[]filter.FilterInterface{
			filter.NewDomainFilter(*url),
			filter.NewExtensionFilter([]string{"js", "css"}),
			filter.NewMimeTypeFilter([]string{"text", "javascript"}, requestTimeout),
		},
		[]requestProcessor.ProcessorInterface{},
		[]responseProcessor.ProcessorInterface{
			responseProcessor.NewSnapshotProcessor(*dataFilePath),
		},
		10,
		*snapshotFilePath,
	)

	if *useState {
		c.LoadSnapshot()
	} else {
		c.Crawl(*url)
	}

	c.Wait()
}
