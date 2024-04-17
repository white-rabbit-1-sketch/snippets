package crawler

import (
	"context"
	"crawler/crawler/collector/request"
	"fmt"
	"github.com/gookit/color"
	"os"
	"os/exec"
	"sync"
	"time"
)

type ConsoleDumper struct {
	externalCtx       context.Context
	internalCtx       context.Context
	internalCtxCancel context.CancelFunc
	wg                sync.WaitGroup
	requestDispatcher *RequestDispatcher
	requestBuffer     []*request.Request
	requestBufferSize int
	requestNumber     int
}

func NewConsoleDumper(
	externalCtx context.Context,
	requestDispatcher *RequestDispatcher,
	maxBufferSize int,
) *ConsoleDumper {
	internalCtx, internalCtxCancel := context.WithCancel(context.Background())

	return &ConsoleDumper{
		externalCtx:       externalCtx,
		internalCtx:       internalCtx,
		internalCtxCancel: internalCtxCancel,
		requestDispatcher: requestDispatcher,
		requestBufferSize: maxBufferSize,
	}
}

func (d *ConsoleDumper) Start() {
	generalMutex := sync.Mutex{}
	isShutDownRequested := false

	d.wg.Add(1)
	go func() {
		defer d.wg.Done()

		select {
		case <-d.externalCtx.Done():
			generalMutex.Lock()
			isShutDownRequested = true
			generalMutex.Unlock()

			d.requestDispatcher.Wait()
			time.Sleep(time.Second * 2)
			d.internalCtxCancel()

			return
		}
	}()

	d.wg.Add(1)
	go func() {
		defer d.wg.Done()

		for {
			select {
			case <-d.internalCtx.Done():
				return
			default:
				d.clrscr()

				red := color.FgRed.Render
				green := color.FgGreen.Render
				blue := color.FgBlue.Render
				yellow := color.FgYellow.Render
				cyan := color.FgCyan.Render

				crawlerRuntimeState := green("running")
				generalMutex.Lock()
				if isShutDownRequested {
					crawlerRuntimeState = red("stopping")
				}
				generalMutex.Unlock()

				fmt.Printf("%s is %s\n",
					yellow("Crawler"),
					crawlerRuntimeState,
				)

				for i, req := range d.requestBuffer {
					stateTitle := green(req.GetStateTitle())

					if req.IsFiltered() {
						stateTitle = blue(req.GetStateTitle())
					} else if req.IsError() {
						stateTitle = red(req.GetStateTitle())
					} else if req.IsCanceled() {
						stateTitle = yellow(req.GetStateTitle())
					}

					stateDetails := req.GetStateDetails()
					if len(stateDetails) == 0 {
						stateDetails = "Details not provided"
					}

					fmt.Printf("[%d][%s; %s]: %s\n",
						d.requestNumber-len(d.requestBuffer)+i,
						stateTitle,
						yellow(stateDetails),
						cyan(req.GetUrl()),
					)
				}

				time.Sleep(time.Second * 1)
			}
		}
	}()
}

func (d *ConsoleDumper) Wait() {
	d.wg.Wait()
}

func (d *ConsoleDumper) DumpRequest(req *request.Request) {
	d.requestBuffer = append(d.requestBuffer, req)
	d.requestNumber++
	if len(d.requestBuffer) > d.requestBufferSize {
		d.requestBuffer = d.requestBuffer[1:]
	}
}

func (d *ConsoleDumper) clrscr() {
	cmd := exec.Command("clear")
	cmd.Stdout = os.Stdout
	cmd.Run()
}
