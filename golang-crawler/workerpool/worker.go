package workerpool

import (
	"context"
	"sync"
)

type Worker struct {
	ctx             context.Context
	wg              *sync.WaitGroup
	taskHandleQueue chan Task
}

func NewWorker(
	ctx context.Context,
	wg *sync.WaitGroup,
	taskHandleQueue chan Task,
) *Worker {
	return &Worker{
		ctx:             ctx,
		wg:              wg,
		taskHandleQueue: taskHandleQueue,
	}
}

func (w *Worker) Start() {
	w.wg.Add(1)

	go func() {
		defer w.wg.Done()
		for {
			select {
			case task := <-w.taskHandleQueue:
				task.Execute()
			case <-w.ctx.Done():
				for {
					select {
					case task := <-w.taskHandleQueue:
						task.Cancel()
					default:
						return
					}
				}
			}
		}
	}()
}
