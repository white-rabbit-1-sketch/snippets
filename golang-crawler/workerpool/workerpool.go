package workerpool

import (
	"context"
	"sync"
)

type WorkerPool struct {
	ctx           context.Context
	wg            sync.WaitGroup
	taskQueue     chan Task
	taskQueueSize int
	workersCount  int
	workerList    []*Worker
}

func NewWorkerPool(ctx context.Context, workersCount int, queueSize int) *WorkerPool {
	workerPool := &WorkerPool{
		ctx:           ctx,
		taskQueue:     make(chan Task, queueSize),
		workerList:    make([]*Worker, workersCount),
		workersCount:  workersCount,
		taskQueueSize: queueSize,
	}

	return workerPool
}

func (w *WorkerPool) Start() {
	for i := 0; i < w.workersCount; i++ {
		w.workerList[i] = NewWorker(w.ctx, &w.wg, w.taskQueue)
		w.workerList[i].Start()
	}
}

func (w *WorkerPool) AddTask(task Task) {
	go func() {
		select {
		case <-w.ctx.Done():
			return
		case w.taskQueue <- task:
		}
	}()
}

func (w *WorkerPool) Wait() {
	w.wg.Wait()
}
