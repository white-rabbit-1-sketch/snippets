package workerpool

type Task struct {
	HandlerFunc func()
	CancelFunc  func()
}

func (t *Task) Execute() {
	t.HandlerFunc()
}

func (t *Task) Cancel() {
	t.CancelFunc()
}
